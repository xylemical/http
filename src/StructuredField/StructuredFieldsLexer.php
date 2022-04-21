<?php

namespace Xylemical\Http\StructuredField;

use Xylemical\Parser\Exception\UnexpectedTokenException;
use Xylemical\Parser\Lexer;
use Xylemical\Parser\TokenStream;

/**
 * Provides the lexer for Structured Fields specification.
 */
class StructuredFieldsLexer extends Lexer {

  /**
   * The target field to generate.
   *
   * @var string
   */
  protected string $target;

  /**
   * StructuredFieldsLexer constructor.
   *
   * @param string $target
   *   The target type of header.
   */
  public function __construct(string $target) {
    $this->target = $target;
  }

  /**
   * {@inheritdoc}
   */
  public function generate(TokenStream $stream): ?StructuredFieldInterface {
    return match ($this->target) {
      'item' => $this->doItem($stream, ['leading' => TRUE, 'trailing' => TRUE]),
      'dictionary' => $this->doDictionary($stream),
      'list' => $this->doList($stream),
      default => NULL,
    };
  }

  /**
   * Parse a dictionary.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The stream.
   *
   * @return \Xylemical\Http\StructuredField\Dictionary
   *   The dictionary or NULL.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function doDictionary(TokenStream $stream): Dictionary {
    $map = [];
    $stream->optional('ws');
    if (count($stream)) {
      while ($key = $this->doKey($stream)) {
        if ($stream->optional('delimiter', '=')) {
          $item = $this->doItemOrInnerSequence($stream);
        }
        else {
          $parameters = $this->doParameters($stream);
          $item = new Item(new BareItem(TRUE), $parameters);
        }
        $map[(string) $key] = $item;

        $stream->optionalOneOf(['ws', 'ows']);
        if (!$stream->optional('delimiter', ',')) {
          break;
        }
        $stream->optionalOneOf(['ws', 'ows']);
      }
    }
    return new Dictionary($map);
  }

  /**
   * Parse a sequence.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The stream.
   *
   * @return \Xylemical\Http\StructuredField\Sequence|null
   *   The list or NULL.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function doList(TokenStream $stream): ?Sequence {
    $sequence = [];
    $stream->optionalOneOf(['ws', 'ows']);
    if (count($stream)) {
      while ($item = $this->doItemOrInnerSequence($stream)) {
        $sequence[] = $item;

        $stream->optionalOneOf(['ws', 'ows']);
        if (!$stream->optional('delimiter', ',')) {
          break;
        }

        $stream->optionalOneOf(['ws', 'ows']);
      }
    }
    return new Sequence($sequence);
  }

  /**
   * Parses an item or inner sequence.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The token stream.
   *
   * @return \Xylemical\Http\StructuredField\StructuredFieldSequenceInterface
   *   The item or sequence.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function doItemOrInnerSequence(TokenStream $stream): StructuredFieldSequenceInterface {
    if ($stream->is('delimiter', '(')) {
      return $this->doInnerSequence($stream);
    }
    return $this->doItem($stream);
  }

  /**
   * Parse an item.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The token stream.
   * @param array $whitespace
   *   Allows whitespace to be optionally removed. Uses the indexes:
   *   - 'leading' to remove optionally remove leading whitespaces
   *   - 'trailing' to remove optionally remove trailing whitespaces.
   *
   * @return \Xylemical\Http\StructuredField\Item
   *   The item or NULL.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function doItem(TokenStream $stream, array $whitespace = []): Item {
    if (!empty($whitespace['leading'])) {
      $stream->optional('ws');
    }

    $bareItem = $this->doBareItem($stream);
    $parameters = $this->doParameters($stream);

    if (!empty($whitespace['trailing'])) {
      $stream->optional('ws');
    }
    return new Item($bareItem, $parameters);
  }

  /**
   * Parse an inner list.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The token stream.
   *
   * @return \Xylemical\Http\StructuredField\InnerSequence
   *   The inner sequence.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function doInnerSequence(TokenStream $stream): InnerSequence {
    $sequence = [];

    $stream->expect('delimiter', '(');
    while (!$stream->is('delimiter', ')')) {
      $stream->optional('ws');
      $sequence[] = $this->doItem($stream);
      if (!$stream->optional('ws') && !$stream->is('delimiter', ')')) {
        throw new UnexpectedTokenException('Expected close of inner sequence.', $stream->peek());
      }
    }

    $stream->optional('ws');
    $stream->expect('delimiter', ')');
    return new InnerSequence($sequence, $this->doParameters($stream));
  }

  /**
   * Parse parameters.
   *
   * @return \Xylemical\Http\StructuredField\Parameters
   *   The parameters.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function doParameters(TokenStream $stream): Parameters {
    $parameters = new Parameters();
    while ($stream->is('delimiter', ';')) {
      $stream->consume();
      $stream->optional('ws');

      $key = $this->doKey($stream);
      if ($stream->optional('delimiter', '=')) {
        $bareItem = $this->doBareItem($stream);
      }
      else {
        $bareItem = new BareItem(TRUE);
      }

      $parameters->set($key, $bareItem);
    }
    return $parameters;
  }

  /**
   * Parse a key.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The stream.
   *
   * @return \Xylemical\Http\StructuredField\Key
   *   The key.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function doKey(TokenStream $stream): Key {
    $token = $stream->expect('sf-token');
    if (!preg_match('/^[a-z\x61-\x7A*][a-z0-9_\-.*\x61-\x7A]*$/', $token->getToken())) {
      throw new UnexpectedTokenException('Expecting a key.', $token);
    }
    return new Key($token->getToken());
  }

  /**
   * Parse a bare item.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The stream.
   *
   * @return \Xylemical\Http\StructuredField\StructuredFieldItemInterface
   *   The bare item.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function doBareItem(TokenStream $stream): StructuredFieldItemInterface {
    $token = $stream->expectOneOf([
      'sf-decimal',
      'sf-integer',
      'sf-string',
      'sf-token',
      'sf-binary',
      'sf-boolean',
    ]);

    return match ($token->getType()) {
      'sf-decimal' => new BareItem(floatval($token->getToken())),
      'sf-integer' => new BareItem(intval($token->getToken())),
      'sf-boolean' => new BareItem($token->getToken() === '?1'),
      'sf-binary' => new ByteSequence(base64_decode(substr($token->getToken(), 1, -1))),
      'sf-string' => new BareItem(preg_replace('#\\\\(\\\\|")#', '$1', $token->getToken())),
      default => new BareItem($token->getToken())
    };
  }

}
