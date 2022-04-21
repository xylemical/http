<?php

namespace Xylemical\Http\Processor;

use Xylemical\Http\Exception\SyntaxException;
use Xylemical\Http\HeaderItemInterface;
use Xylemical\Http\ParserFactoryInterface;

/**
 * Provides the HTTP header parsers.
 *
 * @see https://datatracker.ietf.org/doc/html/rfc7230
 */
class HttpParserFactory implements ParserFactoryInterface {

  protected const TYPES = [
    'http-ows',
    'http-bws' => "[ \t]*",
    'http-rws' => "[ \t]",
    'http-token' => '[!#$%&\'*+\-.^_`|~0-9a-zA-Z]+',
    'http-tchar' => '[!#$%&\'*+\-.^_`|~0-9a-zA-Z]',
    // DQUOTE *( qdtext / quoted-pair ) DQUOTE
    'http-quoted-string' => '""',
    // HTAB / SP /%x21 / %x23-5B / %x5D-7E / obs-text
    'http-qdtext' => '',
    // %x80-FF
    'http-obstext' => '',
    // "(" *( ctext / quoted-pair / comment ) ")"
    'http-comment' => '',
    // HTAB / SP / %x21-27 / %x2A-5B / %x5D-7E / obs-text
    'http-ctext' => '',
    // "\" ( HTAB / SP / VCHAR / obs-text )
    'http-quoted-pair' => '',

    '',
  ];

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return 'http';
  }

  /**
   * {@inheritdoc}
   */
  public function getTypes(): array {
    return array_keys(static::TYPES);
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getPattern(string $type, ParserFactoryInterface $factory): string {
    return static::TYPES[$type] ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function parse(string $type, array &$match): HeaderItemInterface {
    throw new SyntaxException();
  }

}
