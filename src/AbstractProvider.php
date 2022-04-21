<?php

namespace Xylemical\Http;

use Xylemical\Parser\Lexer;
use Xylemical\Parser\Tokenizer;

/**
 * Provides a base header provider.
 */
abstract class AbstractProvider implements ProviderInterface {

  /**
   * The common tokenizer.
   *
   * @var \Xylemical\Parser\Tokenizer
   */
  protected Tokenizer $tokenizer;

  /**
   * {@inheritdoc}
   */
  public function getTokenizer(string $header): Tokenizer {
    if (!isset($this->provider)) {
      $this->tokenizer = $this->createTokenizer();
    }
    return $this->tokenizer;
  }

  /**
   * {@inheritdoc}
   */
  abstract public function getLexer(string $header): Lexer;

  /**
   * {@inheritdoc}
   */
  public function getLine(string $header, array $values): string {
    return implode(', ', $values);
  }

  /**
   * {@inheritdoc}
   */
  public function getValue(string $header, string $value): array {
    return [$value];
  }

  /**
   * Creates the common tokenizer.
   *
   * @return \Xylemical\Parser\Tokenizer
   *   The tokenizer.
   */
  abstract protected function createTokenizer(): Tokenizer;

  /**
   * {@inheritdoc}
   */
  public function normalize(string $header): string {
    return Header::normalize($header);
  }

}
