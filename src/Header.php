<?php

namespace Xylemical\Http;

/**
 * Provides a simplified interaction with the header using the structured field.
 */
class Header implements \Stringable {

  /**
   * Predefined header types.
   */
  public const HEADER_TYPES = [];

  /**
   * The structured field.
   *
   * @var \Xylemical\Http\StructuredFieldInterface|null
   */
  protected ?StructuredFieldInterface $item;

  /**
   * Header constructor.
   *
   * @param string $value
   *   The header value.
   * @param string $type
   *   The header type, leave blank for auto-completion.
   */
  public function __construct(string $value, string $type = '') {
    $type = $type ?: (self::HEADER_TYPES[$value] ?? 'list');
    $this->item = Field::parse($type, $value);
  }

  /**
   * {@inheritdoc}
   */
  public function __toString(): string {
    return (string) $this->item;
  }

  /**
   * Check the header is filled with valid data.
   *
   * @return bool
   *   The result.
   */
  public function isValid(): bool {
    return (string) $this !== '';
  }

  /**
   * Normalize the header name.
   *
   * @param string $header
   *   The name.
   *
   * @return string
   *   The normalized header.
   */
  public static function normalize(string $header): string {
    $header = trim($header);
    $header = strtolower($header);
    $header = explode('-', $header);
    $header = array_map('ucfirst', $header);
    return implode('-', $header);
  }

}
