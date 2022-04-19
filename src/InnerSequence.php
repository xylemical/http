<?php

namespace Xylemical\Http;

use Xylemical\Http\Exception\SyntaxException;

/**
 * Provides an inner list for structured fields.
 */
final class InnerSequence extends StructuredField implements StructuredFieldSequenceInterface, \IteratorAggregate, \ArrayAccess, \Countable {

  /**
   * The inner sequence values.
   *
   * @var \Xylemical\Http\StructuredFieldSequenceInterface[]
   */
  protected array $values = [];

  /**
   * The parameters of the inner sequence.
   *
   * @var \Xylemical\Http\Parameters
   */
  protected Parameters $parameters;

  /**
   * InnerSequence constructor.
   *
   * @param \Xylemical\Http\StructuredFieldSequenceInterface[] $values
   *   The values.
   * @param \Xylemical\Http\Parameters $parameters
   *   The parameters.
   */
  public function __construct(array $values, Parameters $parameters) {
    $this->replace($values);
    $this->parameters = $parameters;
  }

  /**
   * Get the parameters.
   *
   * @return \Xylemical\Http\Parameters
   *   The parameters.
   */
  public function getParameters(): Parameters {
    return $this->parameters;
  }

  /**
   * Get the values of the sequence.
   *
   * @return \Xylemical\Http\StructuredFieldSequenceInterface[]
   *   The values.
   */
  public function all(): array {
    return $this->values;
  }

  /**
   * Check the sequence has a value.
   *
   * @param int $index
   *   The value.
   *
   * @return bool
   *   The result.
   */
  public function has(int $index): bool {
    return isset($this->values[$index]);
  }

  /**
   * Get a sequence value with default.
   *
   * @param int $index
   *   The index.
   *
   * @return \Xylemical\Http\StructuredFieldSequenceInterface|null
   *   The value.
   */
  public function get(int $index): ?StructuredFieldSequenceInterface {
    return $this->values[$index] ?? NULL;
  }

  /**
   * Replace all the sequence values.
   *
   * @param array $values
   *   The parameter values.
   *
   * @return $this
   */
  public function replace(array $values = []): static {
    $this->values = [];
    foreach ($values as $value) {
      $this->add($value);
    }
    return $this;
  }

  /**
   * Add a sequence value.
   *
   * @param mixed $value
   *   The value.
   *
   * @return $this
   */
  public function add(mixed $value): static {
    $this->values[] = Item::fromInternal($value);
    return $this;
  }

  /**
   * Set a value for the index.
   *
   * @param int $index
   *   The index.
   * @param mixed $value
   *   The value.
   *
   * @return $this
   */
  public function set(int $index, mixed $value): static {
    $this->values[$index] = Item::fromInternal($value);
    return $this;
  }

  /**
   * Remove an index value.
   *
   * @param int $index
   *   The index.
   *
   * @return $this
   */
  public function remove(int $index): static {
    unset($this->values[$index]);
    $this->values = array_values($this->values);
    return $this;
  }

  /**
   * Clear all the values from the sequence.
   *
   * @return $this
   */
  public function clear(): static {
    $this->values = [];
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator() {
    $values = [];
    foreach (array_keys($this->values) as $key) {
      $values[] = $this->offsetGet($key);
    }
    return new \ArrayIterator($values);
  }

  /**
   * {@inheritdoc}
   *
   * @param mixed $offset
   *   The offset.
   */
  public function offsetExists(mixed $offset): bool {
    return $this->has((int) $offset);
  }

  /**
   * {@inheritdoc}
   *
   * @param mixed $offset
   *   The offset.
   *
   * @return array
   *   The value.
   */
  public function offsetGet(mixed $offset): mixed {
    return $this->get((int) $offset)?->toInternal();
  }

  /**
   * {@inheritdoc}
   *
   * @param mixed $offset
   *   The offset.
   * @param mixed $value
   *   The value.
   */
  public function offsetSet(mixed $offset, mixed $value): void {
    $this->set((int) $offset, $value);
  }

  /**
   * {@inheritdoc}
   *
   * @param mixed $offset
   *   The offset.
   */
  public function offsetUnset(mixed $offset): void {
    $this->remove((int) $offset);
  }

  /**
   * {@inheritdoc}
   */
  public function count() {
    return count($this->values);
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
