<?php

namespace Xylemical\Http\Parser;

use Xylemical\Parser\Tokenizer as BaseTokenizer;

/**
 * Provides the HTTP tokenizer.
 *
 *  @see https://datatracker.ietf.org/doc/html/rfc7230
 */
class Tokenizer extends BaseTokenizer {

  protected const PATTERNS = [
    'ws' => '[ \t]+',
    'delimiter' => '[\(\),/:;<=>?@\\\\\[\]{}]',
    'number' => '-?\d+(?:\.\d+)',
    'string' => '"(?:\\\\[\\\\\t \x21-\x27\x2A-\x5B\x5D-\x7E]|[0-9a-zA-Z!#$%&\'*+\-.^_`|~])*"',
    'token' => '[0-9a-zA-Z!#$%&\'*+\-.^_`|~]+',
    'obs-text' => '[\x80-\xFF]',
  ];

  protected const REFINEMENTS = [
    'number' => [
      'decimal' => '^-?\d+\.\d+$',
      'integer' => '^\d+$',
    ],
  ];

}