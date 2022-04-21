<?php

namespace Xylemical\Http\StructuredField;

use Xylemical\Parser\Tokenizer;

/**
 * Provides tokenization of structured field headers.
 */
class StructuredFieldTokenizer extends Tokenizer {

  /**
   * Tokens defined by the Structured Fields specification.
   */
  protected const PATTERNS = [
    'ws' => '[ ]+',
    'ows' => '[ \t]+',
    'delimiter' => '[\(\);=,]',
    'sf-string' => '"(?:\\\\\\\\|\\\\"|[\x20-\x21\x23-\x5a\x5e-\x7e\[\]])*"',
    'sf-decimal' => '-?\d{1,12}\.\d{1,3}',
    'sf-integer' => '-?\d{1,15}',
    'sf-boolean' => '\?(?:1|0)',
    'sf-binary' => ':(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=)?:',
    'sf-token' => '[a-zA-Z*][a-zA-Z0-9|~.+*!:/\x23-\x27\x5e-\x60\-]*',
  ];

}