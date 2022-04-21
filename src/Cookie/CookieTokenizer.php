<?php

namespace Xylemical\Http\Cookie;

use Xylemical\Parser\Tokenizer;

/**
 * Provides the tokenizer for the cookie header.
 */
class CookieTokenizer extends Tokenizer {

  /**
   * {@inheritdoc}
   */
  protected const PATTERNS = [
    'sp' => '[ ]',
    'eol' => '\n',
    'delimiter' => '[;=]',
    'number' => '[1-9][0-9]*',
    'string' => '"[\x21\x23-\x2b\x2d-\x3a\x3c-\x5b\x5d\x7e]*"',
    'token' => '[a-zA-Z0-9|~.+*!:/\x23-\x27\x5e-\x60\-]+',
    'octet' => '[\x21\x23-\x2b\x2d-\x3a\x3c-\x5b\x5d\x7e]+',
  ];

  /**
   * Check the name is a valid cookie name.
   *
   * @param string $name
   *   The name.
   *
   * @return bool
   *   The result.
   */
  public static function isCookieName(string $name): bool {
    return preg_match('`^' . self::PATTERNS['token'] . '$`', $name);

  }

  /**
   * Check the name is a valid cookie value.
   *
   * @param string $value
   *   The value.
   *
   * @return bool
   *   The result.
   */
  public static function isCookieValue(string $value): bool {
    return preg_match('`^' . self::PATTERNS['string'] . '$`', $value) ||
      preg_match('`^' . self::PATTERNS['token'] . '$`', $value) ||
      preg_match('`^' . self::PATTERNS['octet'] . '$`', $value);
  }

}
