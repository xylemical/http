<?php

namespace Xylemical\Http\StructuredField;

/**
 * Provides a structured field item.
 */
final class Item extends StructuredField implements StructuredFieldSequenceInterface {

  /**
   * The value.
   *
   * @var \Xylemical\Http\StructuredField\StructuredFieldItemInterface
   */
  protected StructuredFieldInterface $value;

  /**
   * The parameters.
   *
   * @var \Xylemical\Http\StructuredField\Parameters
   */
  protected Parameters $parameters;

  /**
   * Item constructor.
   *
   * @param \Xylemical\Http\StructuredField\StructuredFieldItemInterface $value
   *   The value.
   * @param \Xylemical\Http\StructuredField\Parameters $parameters
   *   The parameters.
   */
  public function __construct(StructuredFieldItemInterface $value, Parameters $parameters) {
    $this->value = $value;
    $this->parameters = $parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function toInternal(): mixed {
    $values = [$this->value->toInternal()];
    if ($parameters = $this->parameters->toInternal()) {
      $values['@attributes'] = $parameters;
    }
    return $values;
  }

  /**
   * Get the value.
   *
   * @return \Xylemical\Http\StructuredField\StructuredFieldInterface
   *   The value.
   */
  public function getValue(): StructuredFieldInterface {
    return $this->value;
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
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.2.3
   *
   * @return \Xylemical\Http\Item
   *   The item.
   */
  public static function parse(string &$input): ?StructuredFieldInterface {
    if (!($item = ByteSequence::parse($input))) {
      $item = BareItem::parse($input);
    }
    return new Item($item, Parameters::parse($input));
  }

  /**
   * {@inheritdoc}
   *
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.1.3
   */
  public function serialize(): string {
    return $this->value->serialize() . $this->parameters->serialize();
  }

  /**
   * {@inheritdoc}
   */
  public static function fromInternal(mixed $value): ?StructuredFieldSequenceInterface {
    if (is_array($value)) {
      $parameters = Parameters::fromInternal($value['@attributes'] ?? []);
      unset($value['@attributes']);
      if (count($value) === 1 && ($item = reset($value)) && is_scalar($item)) {
        return new Item(BareItem::fromInternal($item), $parameters);
      }

      $internal = InnerSequence::fromInternal($value);
      $internal->getParameters()->replace($parameters->all());
      return $internal;
    }

    return match (TRUE) {
      is_null($value) => NULL,
      is_scalar($value) => new Item(new BareItem($value), new Parameters()),
      $value instanceof StructuredFieldSequenceInterface => $value,
      default => throw new \InvalidArgumentException(),
    };
  }

}
