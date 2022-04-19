<?php

namespace Xylemical\Http;

use Xylemical\Http\Exception\SyntaxException;

/**
 * Provides a structured field dictionary.
 */
final class Dictionary extends StructuredField implements \IteratorAggregate, \ArrayAccess, \Countable {

  /**
   * The dictionary map.
   *
   * @var \Xylemical\Http\StructuredFieldSequenceInterface[]
   */
  protected array $map = [];

  /**
   * Dictionary constructor.
   *
   * @param \Xylemical\Http\StructuredFieldSequenceInterface[] $map
   *   The map.
   */
  public function __construct(array $map = []) {
    $this->replace($map);
  }

  /**
   * Get the dictionary values.
   *
   * @return \Xylemical\Http\StructuredFieldSequenceInterface[]
   *   The values.
   */
  public function all(): array {
    return $this->map;
  }

  /**
   * Check the dictionary has a value.
   *
   * @param string $key
   *   The value.
   *
   * @return bool
   *   The result.
   */
  public function has(string $key): bool {
    return isset($this->map[$key]);
  }

  /**
   * Get a dictionary value with default.
   *
   * @param string $key
   *   The key.
   * @param \Xylemical\Http\StructuredFieldSequenceInterface|null $default
   *   A default item.
   *
   * @return \Xylemical\Http\StructuredFieldSequenceInterface|null
   *   The value.
   */
  public function get(string $key, ?StructuredFieldSequenceInterface $default = NULL): ?StructuredFieldSequenceInterface {
    return $this->map[$key] ?? $default;
  }

  /**
   * Replace all the dictionary values.
   *
   * @param array $values
   *   The dictionary values.
   *
   * @return $this
   */
  public function replace(array $values = []): static {
    $this->map = [];
    foreach ($values as $key => $value) {
      $this->set($key, $value);
    }
    return $this;
  }

  /**
   * Set a value for the key.
   *
   * @param string $key
   *   The key.
   * @param mixed $value
   *   The value.
   *
   * @return $this
   */
  public function set(string $key, mixed $value): static {
    $this->map[$key] = Item::fromInternal($value);
    return $this;
  }

  /**
   * Remove a dictionary value.
   *
   * @param string $key
   *   The key.
   *
   * @return $this
   */
  public function remove(string $key): static {
    unset($this->map[$key]);
    return $this;
  }

  /**
   * Clear all the dictionary values.
   *
   * @return $this
   */
  public function clear(): static {
    $this->map = [];
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator() {
    $values = [];
    foreach (array_keys($this->map) as $key) {
      $values[$key] = $this->offsetGet($key);
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
    return $this->has((string) $offset);
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
    return $this->get((string) $offset)?->toInternal();
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
    $this->set((string) $offset, $value);
  }

  /**
   * {@inheritdoc}
   *
   * @param mixed $offset
   *   The offset.
   */
  public function offsetUnset(mixed $offset): void {
    $this->remove((string) $offset);
  }

  /**
   * {@inheritdoc}
   */
  public function count() {
    return count($this->map);
  }

  /**
   * {@inheritdoc}
   *
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.2.2
   *
   * @return \Xylemical\Http\Dictionary
   *   The dictionary.
   */
  public static function parse(string &$input): ?StructuredFieldInterface {
    $map = [];
    $count = 0;
    do {
      if ($count++ > 0) {
        if (!str_starts_with($input, ',')) {
          throw new SyntaxException();
        }
        $input = substr($input, 1);
      }

      $input = ltrim($input, $count > 1 ? " \t" : ' ');
      if (!$input && $count === 1) {
        break;
      }

      $key = Key::parse($input);

      if (!str_starts_with($input, '=')) {
        $map[(string) $key] = new Item(new BareItem(TRUE), Parameters::parse($input));
        continue;
      }

      $input = substr($input, 1);

      if ($value = InnerSequence::parse($input)) {
        $map[(string) $key] = $value;
      }
      elseif ($value = Item::parse($input)) {
        $map[(string) $key] = $value;
      }

      $input = ltrim($input, " \t");
    } while ($input);

    return new Dictionary($map);
  }

  /**
   * {@inheritdoc}
   *
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.1.2
   */
  public function serialize(): string {
    $output = '';
    $count = 0;
    foreach ($this->map as $key => $value) {
      $output .= $count++ > 0 ? ', ' : '';

      $output .= (new Key($key))->serialize();

      if ($value instanceof Item) {
        $item = $value->getValue();
        if ($item instanceof BareItem && $item->getValue() === TRUE) {
          $output .= $value->getParameters()->serialize();
          continue;
        }
      }

      $output .= '=' . $value->serialize();
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function toInternal(): mixed {
    $values = [];
    foreach ($this->map as $key => $value) {
      $values[$key] = $value->toInternal();
    }
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public static function fromInternal(mixed $value): ?Dictionary {
    if (!is_array($value)) {
      return NULL;
    }

    $values = [];
    foreach ($value as $key => $item) {
      if (!is_null($object = Item::fromInternal($item))) {
        $values[$key] = $object;
      }
    }

    return new Dictionary($values);
  }

}
