<?php

namespace Xylemical\Http\StructuredField;

use Xylemical\Parser\Exception\SyntaxException;

/**
 * Provides an inner list for structured fields.
 */
final class InnerSequence extends StructuredField implements StructuredFieldSequenceInterface, \IteratorAggregate, \ArrayAccess, \Countable {

  use SequenceTrait;

  /**
   * The parameters of the inner sequence.
   *
   * @var \Xylemical\Http\StructuredField\Parameters
   */
  protected Parameters $parameters;

  /**
   * InnerSequence constructor.
   *
   * @param \Xylemical\Http\StructuredField\StructuredFieldSequenceInterface[] $values
   *   The values.
   * @param \Xylemical\Http\StructuredField\Parameters $parameters
   *   The parameters.
   */
  public function __construct(array $values, Parameters $parameters) {
    $this->replace($values);
    $this->parameters = $parameters;
  }

  /**
   * Get the parameters.
   *
   * @return \Xylemical\Http\StructuredField\Parameters
   *   The parameters.
   */
  public function getParameters(): Parameters {
    return $this->parameters;
  }

  /**
   * {@inheritdoc}
   *
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.2.1.2
   *
   * @return \Xylemical\Http\InnerSequence|null
   *   The inner sequence or NULL.
   */
  public static function parse(string &$input): ?StructuredFieldInterface {
    if (!str_starts_with($input, '(')) {
      return NULL;
    }

    $values = [];
    $input = substr($input, 1);
    do {
      $input = ltrim($input, " \t");

      if (str_starts_with($input, ')')) {
        $input = substr($input, 1);
        return new InnerSequence($values, Parameters::parse($input));
      }

      $values[] = Item::parse($input);
    } while (str_starts_with($input, ')') || str_starts_with($input, ' '));

    throw new SyntaxException();
  }

  /**
   * {@inheritdoc}
   *
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.1.1.1
   */
  public function serialize(): string {
    $output = '(';
    $count = 0;
    foreach ($this->values as $value) {
      $output .= $count++ > 0 ? ' ' : '';
      $output .= $value->serialize();
    }
    $output .= ')';
    return $output . $this->parameters->serialize();
  }

  /**
   * {@inheritdoc}
   */
  public function toInternal(): mixed {
    $values = [];
    foreach ($this->values as $key => $value) {
      $values[$key] = $value->toInternal();
    }
    if ($parameters = $this->parameters->toInternal()) {
      $values['@attributes'] = $parameters;
    }
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public static function fromInternal(mixed $value): ?InnerSequence {
    if (!is_array($value)) {
      if ($value instanceof InnerSequence) {
        return $value;
      }
      return NULL;
    }

    $parameters = Parameters::fromInternal($value['@attributes'] ?? []);
    unset($value['@attributes']);

    $object = [];
    foreach ($value as $item) {
      if (!is_null($item = Item::fromInternal($item))) {
        $object[] = $item;
      }
    }

    return new InnerSequence($object, $parameters);
  }

}
