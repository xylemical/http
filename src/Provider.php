<?php

namespace Xylemical\Http;

use Xylemical\Parser\Exception\SyntaxException;
use Xylemical\Parser\Lexer;
use Xylemical\Parser\Tokenizer;

/**
 * Provides support for combined header providers.
 */
class Provider implements ProviderInterface {

  /**
   * The providers.
   *
   * @var \Xylemical\Http\ProviderInterface[]
   */
  protected array $providers = [];

  /**
   * {@inheritdoc}
   */
  public function applies(string $header): bool {
    foreach ($this->providers as $provider) {
      if ($provider->applies($header)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize(string $header): string {
    foreach ($this->providers as $provider) {
      if ($provider->applies($header)) {
        return $provider->normalize($header);
      }
    }
    return Header::normalize($header);
  }

  /**
   * {@inheritdoc}
   */
  public function getTokenizer(string $header): Tokenizer {
    foreach ($this->providers as $provider) {
      if ($provider->applies($header)) {
        return $provider->getTokenizer($header);
      }
    }
    throw new SyntaxException();
  }

  /**
   * {@inheritdoc}
   */
  public function getLexer(string $header): Lexer {
    foreach ($this->providers as $provider) {
      if ($provider->applies($header)) {
        return $provider->getLexer($header);
      }
    }
    throw new SyntaxException();
  }

  /**
   * {@inheritdoc}
   */
  public function getLine(string $header, array $values): string {
    foreach ($this->providers as $provider) {
      if ($provider->applies($header)) {
        return $provider->getLine($header, $values);
      }
    }
    throw new SyntaxException();
  }

  /**
   * {@inheritdoc}
   */
  public function getValue(string $header, string $value): array {
    foreach ($this->providers as $provider) {
      if ($provider->applies($header)) {
        return $provider->getValue($header, $value);
      }
    }
    throw new SyntaxException();
  }

}
