<?php

namespace Xylemical\Http;

use Xylemical\Http\Exception\SyntaxException;

/**
 * Provides a structured field sequence.
 */
final class Sequence extends StructuredField implements \IteratorAggregate, \ArrayAccess, \Countable {

  /**
   * The values for the sequence.
   *
   * @var \Xylemical\Http\StructuredFieldSequenceInterface[]
   */
  protected array $values = [];

  /**
   * Sequence constructor.
   *
   * @param \Xylemical\Http\StructuredFieldSequenceInterface[] $values
   *   The values.
   */
  public function __construct(array $values = []) {
    $this->replace($values);
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
