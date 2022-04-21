<?php

namespace Xylemical\Http\StructuredField;

/**
 * Provides structured field parameters.
 */
final class Parameters extends StructuredField implements \IteratorAggregate, \ArrayAccess, \Countable {

  /**
   * The parameter values.
   *
   * @var \Xylemical\Http\StructuredField\BareItem[]
   */
  protected array $values;

  /**
   * Parameters constructor.
   *
   * @param array $parameters
   *   The parameters.
   */
  public function __construct(array $parameters = []) {
    $this->replace($parameters);
  }

  /**
   * Get the parameter values.
   *
   * @return \Xylemical\Http\StructuredField\BareItem[]
   *   The values.
   */
  public function all(): array {
    return $this->values;
  }

  /**
   * Check the parameter has a value.
   *
   * @param string $parameter
   *   The value.
   *
   * @return bool
   *   The result.
   */
  public function has(string $parameter): bool {
    return isset($this->values[$parameter]);
  }

  /**
   * Get a parameter value with default.
   *
   * @param string $parameter
   *   The parameter.
   * @param mixed $default
   *   A default item.
   *
   * @return \Xylemical\Http\StructuredField\BareItem|null
   *   The value.
   */
  public function get(string $parameter, mixed $default = NULL): ?BareItem {
    return $this->values[$parameter] ?? BareItem::fromInternal($default);
  }

  /**
   * Replace all the parameter values.
   *
   * @param array $values
   *   The parameter values.
   *
   * @return $this
   */
  public function replace(array $values = []): static {
    $this->values = [];
    foreach ($values as $key => $value) {
      $this->set($key, $value);
    }
    return $this;
  }

  /**
   * Set a value for the parameter.
   *
   * @param string $key
   *   The parameter key.
   * @param mixed $value
   *   The parameter value.
   *
   * @return $this
   */
  public function set(string $key, mixed $value): static {
    $this->values[$key] = BareItem::fromInternal($value);
    return $this;
  }

  /**
   * Remove a parameter value.
   *
   * @param string $key
   *   The parameter key.
   *
   * @return $this
   */
  public function remove(string $key): static {
    unset($this->values[$key]);
    return $this;
  }

  /**
   * Clear all the parameter values.
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
   * @return scalar|null
   *   The value.
   */
  public function offsetGet(mixed $offset): mixed {
    return $this->get((string) $offset)?->getValue();
  }

  /**
   * {@inheritdoc}
   *
   * @param mixed $offset
   *   The offset.
   * @param mixed $value
   *   The values.
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
    return count($this->values);
  }

  /**
   * {@inheritdoc}
   *
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.2.3.2
   *
   * @return \Xylemical\Http\Parameters
   *   The parameters.
   */
  public static function parse(string &$input): ?StructuredFieldInterface {
    if (!str_starts_with($input, ';')) {
      return new Parameters();
    }

    $values = [];
    do {
      $input = substr($input, 1);
      $input = ltrim($input, ' ');
      $key = Key::parse($input);

      $value = new BareItem(TRUE);
      if (str_starts_with($input, '=')) {
        $input = substr($input, 1);
        $value = BareItem::parse($input);
      }

      $values[(string) $key] = $value;
    } while (str_starts_with($input, ';'));
    return new Parameters($values);
  }

  /**
   * {@inheritdoc}
   *
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.1.1.2
   */
  public function serialize(): string {
    $output = '';
    foreach ($this->values as $key => $value) {
      $output .= ';';
      $output .= (new Key($key))->serialize();
      if ($value->getValue() !== TRUE) {
        $output .= '=';
        $output .= $value->serialize();
      }
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
  public static function fromInternal(mixed $value): Parameters {
    if (!is_array($value)) {
      return new Parameters();
    }

    $values = [];
    foreach ($value as $key => $item) {
      if (!is_null($item = BareItem::fromInternal($item))) {
        $values[$key] = $item;
      }
    }

    return new Parameters($values);
  }

}
