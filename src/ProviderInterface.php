<?php

namespace Xylemical\Http;

use Xylemical\Parser\Tokenizer;
use Xylemical\Parser\Lexer;

/**
 * Provides for processing the headers.
 */
interface ProviderInterface {

  /**
   * Check the header provider applies to the header.
   *
   * @param string $header
   *   The lowercase header.
   *
   * @return bool
   *   The result.
   */
  public function applies(string $header): bool;

  /**
   * Normalizes a header name.
   *
   * @param string $header
   *   The lowercase header.
   *
   * @return string
   *   The normalized header name.
   */
  public function normalize(string $header): string;

  /**
   * Get the tokenizer for the header.
   *
   * @param string $header
   *   The header.
   *
   * @return \Xylemical\Parser\Tokenizer
   *   The tokenizer.
   */
  public function getTokenizer(string $header): Tokenizer;

  /**
   * Get the lexer for the header.
   *
   * @param string $header
   *   The header.
   *
   * @return \Xylemical\Parser\Lexer
   */
  public function getLexer(string $header): Lexer;

  /**
   * Serializes the header.
   *
   * @param string $header
   *   The header.
   * @param mixed $item
   *   The object defining the header.
   *
   * @return string
   *   The header value.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  public function serialize(string $header, mixed $item): string;

  /**
   * Converts the header the line into a parsable value.
   *
   * @param string $header
   *   The lowercase header.
   * @param string[] $values
   *   The values.
   *
   * @return string
   *   The header line.
   */
  public function getLine(string $header, array $values): string;

  /**
   * Get the header lines from the generated string.
   *
   * @param string $header
   *   The lowercase header.
   * @param string $value
   *   The values.
   *
   * @return string[]
   *   The header lines.
   */
  public function getValue(string $header, string $value): array;

}
