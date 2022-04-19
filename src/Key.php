<?php

namespace Xylemical\Http;

use Xylemical\Http\Exception\SyntaxException;

/**
 * Provides a structured field key.
 */
final class Key extends StructuredField {

  /**
   * The value of the key.
   *
   * @var string
   */
  protected string $value;

  /**
   * Key constructor.
   *
   * @param string $value
   *   The value.
   */
  public function __construct(string $value) {
    $this->value = $value;
  }

  /**
   * Get the key value.
   *
   * @return string
   *   The value.
   */
  public function getValue(): string {
    return $this->value;
  }

  /**
   * {@inheritdoc}
   *
   * @return \Xylemical\Http\Key
   *   The key.
   */
  public static function parse(string &$input): ?StructuredFieldInterface {
    if (!preg_match('#^[a-z*][a-z0-9.*_\-]*#', $input, $match)) {
      throw new SyntaxException();
    }
    $input = substr($input, strlen($match[0]));
    return new Key($match[0]);
  }

  /**
   * {@inheritdoc}
   *
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.1.1.3
   */
  public function serialize(): string {
    if (!preg_match('#^[a-z*][a-z0-9.*_\-]*$#', $this->value)) {
      throw new SyntaxException();
    }
    return $this->value;
  }

  /**
   * {@inheritdoc}
   */
  public function toInternal(): string {
    return $this->value;
  }

  /**
   * {@inheritdoc}
   */
  public static function fromInternal(mixed $value): ?StructuredFieldInterface {
    return match(TRUE) {
      is_scalar($value) or $value instanceof \Stringable => new Key((string) $value),
      default => NULL,
    };
  }

}
