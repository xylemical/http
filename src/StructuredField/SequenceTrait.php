<?php

namespace Xylemical\Http\StructuredField;

/**
 * Provides common functionality for sequences.
 *
 * @see \IteratorAggregate
 * @see \ArrayAccess
 * @see \Countable
 */
trait SequenceTrait {

  /**
   * The sequence values.
   *
   * @var \Xylemical\Http\StructuredField\StructuredFieldSequenceInterface
   */
  protected array $values = [];

  /**
   * Get the values of the sequence.
   *
   * @return \Xylemical\Http\StructuredField\StructuredFieldSequenceInterface[]
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
   * @return \Xylemical\Http\StructuredField\StructuredFieldSequenceInterface|null
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

}