<?php

namespace Xylemical\Http;

/**
 * Provides the base for structured fields.
 */
interface StructuredFieldInterface extends \Stringable {

  /**
   * Parse an input string.
   *
   * @param string $input
   *   The input string, updated on success.
   *
   * @return \Xylemical\Http\StructuredFieldInterface|null
   *   An instance, or NULL if not the input.
   *
   * @throws \Xylemical\Http\Exception\SyntaxException
   */
  public static function parse(string &$input): ?StructuredFieldInterface;

  /**
   * Serialize the item.
   *
   * @return string
   *   The serialized output.
   *
   * @throws \Xylemical\Http\Exception\SyntaxException
   */
  public function serialize(): string;

  /**
   * Get the structured field into a PHP value.
   *
   * @return mixed
   *   The PHP value.
   */
  public function toInternal(): mixed;

  /**
   * Generate the structured field from a PHP value.
   *
   * @param mixed $value
   *   The PHP value.
   *
   * @return \Xylemical\Http\StructuredFieldInterface|null
   *   The object or NULL.
   */
  public static function fromInternal(mixed $value): ?StructuredFieldInterface;

}
