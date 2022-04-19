<?php

namespace Xylemical\Http;

use Xylemical\Http\Exception\SyntaxException;

/**
 * Provides the structure field processing.
 */
final class Field {

  /**
   * Create a structured field from string.
   *
   * @param string $type
   *   One of 'item', 'list', 'dictionary'.
   * @param string $input
   *   The input for the field.
   *
   * @return \Xylemical\Http\StructuredFieldInterface|null
   *   The structured field data or NULL.
   *
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.2
   */
  public static function parse(string $type, string $input): ?StructuredFieldInterface {
    try {
      $input = trim($input, ' ');

      switch ($type) {
        case 'item':
          $item = Item::parse($input);
          break;

        case 'list':
          $item = Sequence::parse($input);
          break;

        case 'dictionary':
          $item = Dictionary::parse($input);
          break;

        default:
          return NULL;
      }

      if ($input) {
        throw new SyntaxException();
      }

      return $item;
    }
    catch (SyntaxException) {
      return NULL;
    }
  }

  /**
   * Convert to dictionary, sequence or item.
   *
   * @param mixed $value
   *   The normalized PHP value.
   *
   * @return \Xylemical\Http\StructuredFieldInterface|null
   *   The created item.
   */
  public static function fromInternal(mixed $value): ?StructuredFieldInterface {
    if ($object = Sequence::fromInternal($value)) {
      return $object;
    }
    if ($object = Dictionary::fromInternal($value)) {
      return $object;
    }
    return Item::fromInternal($value);
  }

}
