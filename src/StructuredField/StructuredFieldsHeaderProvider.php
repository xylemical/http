<?php

namespace Xylemical\Http\StructuredField;

use Xylemical\Http\AbstractProvider;
use Xylemical\Parser\Exception\SyntaxException;
use Xylemical\Parser\Lexer;
use Xylemical\Parser\Tokenizer;

/**
 * Provides support for structured field headers.
 *
 * @see https://datatracker.ietf.org/doc/html/rfc8941
 */
class StructuredFieldsHeaderProvider extends AbstractProvider {

  /**
   * The lexer for item headers.
   *
   * @var \Xylemical\Parser\Lexer
   */
  protected Lexer $itemLexer;

  /**
   * The lexer for list headers.
   *
   * @var \Xylemical\Parser\Lexer
   */
  protected Lexer $listLexer;

  /**
   * The lexer for dictionary headers.
   *
   * @var \Xylemical\Parser\Lexer
   */
  protected Lexer $dictionaryLexer;

  /**
   * Get the type of the header.
   *
   * @param string $header
   *   The header.
   *
   * @return string
   *   The lexer type.
   */
  protected function getType(string $header): string {
    return $this->headers[$header] ?? 'item';
  }

  /**
   * Get the item lexer.
   * 
   * @return \Xylemical\Parser\Lexer
   *   The lexer.
   */
  protected function getItemLexer(): Lexer {
    if (!isset($this->itemLexer)) {
      $this->itemLexer = new StructuredFieldsLexer('item');
    }
    return $this->itemLexer;
  }

  /**
   * Get the list lexer.
   *
   * @return \Xylemical\Parser\Lexer
   *   The lexer.
   */
  protected function getListLexer(): Lexer {
    if (!isset($this->listLexer)) {
      $this->listLexer = new StructuredFieldsLexer('list');
    }
    return $this->listLexer;
  }

  /**
   * Get the dictionary lexer.
   *
   * @return \Xylemical\Parser\Lexer
   *   The lexer.
   */
  protected function getDictionaryLexer(): Lexer {
    if (!isset($this->dictionaryLexer)) {
      $this->dictionaryLexer = new StructuredFieldsLexer('dictionary');
    }
    return $this->dictionaryLexer;
  }

  /**
   * {@inheritdoc}
   */
  public function getLexer(string $header): Lexer {
    return match($this->getType($header)) {
      'list' => $this->getListLexer(),
      'dictionary' => $this->getDictionaryLexer(),
      default => $this->getItemLexer()
    };
  }

  /**
   * {@inheritdoc}
   */
  protected function createTokenizer(): Tokenizer {
    return new StructuredFieldTokenizer();
  }

  /**
   * {@inheritdoc}
   */
  public function applies(string $header): bool {
    // TODO: Implement applies() method.
  }

  /**
   * {@inheritdoc}
   */
  public function normalize(string $header): string {
    // TODO: Implement normalize() method.
  }

  /**
   * {@inheritdoc}
   */
  public function serialize(string $header, mixed $item): string {
    if ($item instanceof StructuredFieldInterface) {
      return $item->serialize();
    }
    throw new SyntaxException();
  }

}
