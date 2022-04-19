<?php

namespace Xylemical\Http;

use Xylemical\Http\Exception\SyntaxException;

/**
 * Provides a base for the structured field items.
 */
abstract class StructuredField implements StructuredFieldInterface {

  /**
   * {@inheritdoc}
   */
  public function __toString(): string {
    try {
      return $this->serialize();
    }
    catch (SyntaxException) {
      return '';
    }
  }

}
