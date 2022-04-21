<?php

namespace Xylemical\Http\Cookie;

use Xylemical\Http\AbstractProvider;
use Xylemical\Parser\Exception\SyntaxException;
use Xylemical\Parser\Lexer;
use Xylemical\Parser\Tokenizer;

/**
 * Provides support for processing cookie headers.
 *
 * @see https://datatracker.ietf.org/doc/html/rfc6265
 */
class CookieHeaderProvider extends AbstractProvider {

  /**
   * {@inheritdoc}
   */
  public function getLexer(string $header): Lexer {
    return new CookieLexer();
  }

  /**
   * {@inheritdoc}
   */
  protected function createTokenizer(): Tokenizer {
    return new CookieTokenizer();
  }

  /**
   * {@inheritdoc}
   */
  public function getLine(string $header, array $values): string {
    return implode("\n", $values);
  }

  /**
   * {@inheritdoc}
   */
  public function getValue(string $header, string $value): array {
    return explode("\n", $value);
  }

  /**
   * {@inheritdoc}
   */
  public function applies(string $header): bool {
    return in_array($header, ['set-cookie', 'cookie']);
  }

  /**
   * {@inheritdoc}
   */
  public function serialize(string $header, mixed $item): string {
    if ($item instanceof Cookies) {
      return (string)$item;
    }
    throw new SyntaxException();
  }

}
