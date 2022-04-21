<?php

namespace Xylemical\Http\StructuredField;

/**
 * Provides the base for structured fields.
 */
interface StructuredFieldInterface extends \Stringable {

  /**
   * Serialize the item.
   *
   * @return string
   *   The serialized output.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  public function serialize(): string;

  /**
   * Get the structured field into a PHP value.
   *
   * @return mixed
   *   The PHP value.
   */
  public function toInternal(): mixed;

}
