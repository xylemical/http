<?php

namespace Xylemical\Http\StructuredField;

use Xylemical\Parser\Exception\SyntaxException;

/**
 * Provides a structured field sequence.
 */
final class Sequence extends StructuredField implements \IteratorAggregate, \ArrayAccess, \Countable {

  use SequenceTrait;

  /**
   * The values for the sequence.
   *
   * @var \Xylemical\Http\StructuredField\StructuredFieldSequenceInterface[]
   */
  protected array $values = [];

  /**
   * Sequence constructor.
   *
   * @param array $values
   *   The values.
   */
  public function __construct(array $values = []) {
    $this->replace($values);
  }

  /**
   * {@inheritdoc}
   *
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.2.1
   *
   * @return \Xylemical\Http\Sequence
   *   The sequence.
   */
  public static function parse(string &$input): ?StructuredFieldInterface {
    $values = [];
    $count = 0;
    do {
      if ($count++ > 0) {
        if (!str_starts_with($input, ',')) {
          throw new SyntaxException();
        }
        $input = substr($input, 1);
      }

      $input = ltrim($input, " \t");
      if (!$input && $count === 1) {
        break;
      }

      if ($value = InnerSequence::parse($input)) {
        $values[] = $value;
      }
      elseif ($value = Item::parse($input)) {
        $values[] = $value;
      }

      $input = ltrim($input, " \t");
    } while ($input);

    return new Sequence($values);
  }

  /**
   * {@inheritdoc}
   *
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.1.1
   */
  public function serialize(): string {
    $output = '';
    $count = 0;
    foreach ($this->values as $value) {
      $output .= $count++ > 0 ? ', ' : '';
      $output .= $value->serialize();
    }
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function toInternal(): mixed {
    $values = [];
    foreach ($this->values as $key => $value) {
      $values[$key] = $value->toInternal();
    }
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public static function fromInternal(mixed $value): ?Sequence {
    if (!is_array($value)) {
      return NULL;
    }
    if (range(0, count($value) - 1) !== array_keys($value)) {
      return NULL;
    }
    $values = [];
    foreach ($value as $item) {
      if (!is_null($object = Item::fromInternal($item))) {
        $values[] = $object;
      }
    }
    return new Sequence($values);
  }

}
