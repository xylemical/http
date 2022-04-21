<?php

namespace Xylemical\Http\StructuredField;

use Xylemical\Parser\Exception\SyntaxException;

/**
 * Provides a byte-sequence.
 */
final class ByteSequence extends StructuredField implements StructuredFieldItemInterface {

  /**
   * The byte sequence.
   *
   * @var string
   */
  protected string $bytes;

  /**
   * ByteSequence constructor.
   *
   * @param string $bytes
   *   The bytes.
   */
  public function __construct(string $bytes) {
    $this->bytes = $bytes;
  }

  /**
   * Get the byte sequence.
   *
   * @return string
   *   The bytes.
   */
  public function getBytes(): string {
    return $this->bytes;
  }

  /**
   * Set the byte sequence values.
   *
   * @param string $bytes
   *   The bytes.
   *
   * @return $this
   */
  public function setBytes(string $bytes): static {
    $this->bytes = $bytes;
    return $this;
  }

  /**
   * {@inheritdoc}
   *
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.2.7
   *
   * @return \Xylemical\Http\ByteSequence|null
   *   The byte sequence or NULL.
   */
  public static function parse(string &$input): ?StructuredFieldInterface {
    if (!str_starts_with($input, ':')) {
      return NULL;
    }

    if (!preg_match('#^:(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=)?:#', $input, $match)) {
      throw new SyntaxException();
    }

    $input = substr($input, strlen($match[0]));
    return new ByteSequence(base64_decode($match[0]));
  }

  /**
   * {@inheritdoc}
   *
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.1.8
   */
  public function serialize(): string {
    return ':' . base64_encode($this->bytes) . ':';
  }

  /**
   * {@inheritdoc}
   */
  public function toInternal(): mixed {
    return $this->bytes;
  }

  /**
   * {@inheritdoc}
   */
  public static function fromInternal(mixed $value): ?ByteSequence {
    return match (TRUE) {
      is_string($value) => new ByteSequence($value),
      default => NULL,
    };
  }

}
