<?php

namespace Xylemical\Http\StructuredField;

use Xylemical\Parser\Exception\SyntaxException;

/**
 * Provides a structured field bare item.
 */
final class BareItem extends StructuredField implements StructuredFieldItemInterface {

  /**
   * Regex to match token.
   */
  private const TOKEN_REGEX = '[a-zA-Z*][a-zA-Z0-9|~.+*!:/\x23-\x27\x5e-\x60\-]*';

  /**
   * Regex to match quoted-string.
   */
  private const STRING_REGEX = '"(?:\\\\\\\\|\\\\"|[\x20-\x21\x23-\x5a\x5e-\x7e\[\]])*"';

  /**
   * Regex to match decimal numbers.
   */
  private const FLOAT_REGEX = '-?\d{1,12}\.\d{1,3}';

  /**
   * Regex to match integer numbers.
   */
  private const INT_REGEX = '-?\d{1,15}';

  /**
   * The value of the bare item.
   *
   * @var int|float|string|bool
   */
  private int|float|string|bool $value;

  /**
   * The string represents a token.
   *
   * @var bool
   */
  private bool $token;

  /**
   * BareItem constructor.
   *
   * @param int|float|string|bool $value
   *   The value.
   */
  public function __construct(int|float|string|bool $value) {
    $this->value = $value;
    $this->token = is_string($value);
    if (is_string($value) && str_starts_with($value, '"') && str_ends_with($value, '"')) {
      $this->token = FALSE;
      $this->value = substr($this->value, 1, -1);
    }
  }

  /**
   * Get the value of the bare item.
   *
   * @return int|float|string|bool
   *   The value.
   */
  public function getValue(): int|float|string|bool {
    return $this->value;
  }

  /**
   * {@inheritdoc}
   *
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.2.4
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.2.5
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.2.6
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.2.7
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.2.8
   *
   * @return \Xylemical\Http\BareItem|null
   *   The object or NULL.
   */
  public static function parse(string &$input): ?StructuredFieldInterface {
    if (str_starts_with($input, '?')) {
      $input = substr($input, 1);
      if (str_starts_with($input, '0')) {
        $input = substr($input, 1);
        return new BareItem(FALSE);
      }
      if (str_starts_with($input, '1')) {
        $input = substr($input, 1);
        return new BareItem(TRUE);
      }
      throw new SyntaxException();
    }

    if (str_starts_with($input, '"')) {
      if (!preg_match('#^' . self::STRING_REGEX . '#', $input, $match)) {
        throw new SyntaxException();
      }
      $input = substr($input, strlen($match[0]));
      return new BareItem($match[0]);
    }

    if (preg_match('#^' . self::TOKEN_REGEX . '#', $input, $match)) {
      $input = substr($input, strlen($match[0]));
      return new BareItem($match[0]);
    }

    if (preg_match('#^-?\d+\.\d+#', $input, $match)) {
      if (!preg_match('#^' . self::FLOAT_REGEX . '$#', $match[0])) {
        throw new SyntaxException();
      }
      $input = substr($input, strlen($match[0]));
      return new BareItem(floatval($match[0]));
    }

    if (preg_match('#^-?\d+#', $input, $match)) {
      if (!preg_match('#^' . self::INT_REGEX . '$#', $match[0])) {
        throw new SyntaxException();
      }
      $input = substr($input, strlen($match[0]));
      return new BareItem(intval($match[0]));
    }

    throw new SyntaxException();
  }

  /**
   * {@inheritdoc}
   *
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.1.4
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.1.5
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.1.6
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.1.7
   * @see https://datatracker.ietf.org/doc/html/rfc8941#section-4.1.9
   */
  public function serialize(): string {
    $value = $this->toInternal();
    switch (TRUE) {
      case is_bool($value):
        return '?' . ($value ? '1' : '0');

      case is_int($value):
        if (abs($value) > 999999999999999) {
          throw new SyntaxException();
        }
        return (string) $value;

      case is_float($value):
        $value = round($value, 3, PHP_ROUND_HALF_EVEN);
        if (abs($value) > 999999999999.0) {
          throw new SyntaxException();
        }
        $result = (string) $value;
        return strpos($result, '.') ? $result : "{$result}.0";

      case $this->token:
        if (!preg_match('#^' . self::TOKEN_REGEX . '$#', $value)) {
          throw new SyntaxException();
        }
        return $value;

      default:
        if (!preg_match('#^' . self::STRING_REGEX . '$#', $value)) {
          throw new SyntaxException();
        }
        return $value;

    }
  }

  /**
   * {@inheritdoc}
   */
  public function toInternal(): mixed {
    if (is_string($this->value) && !$this->token) {
      $value = str_replace(
        ['\\', '"'],
        ['\\\\', '\\"'],
        $this->value
      );
      return '"' . $value . '"';
    }
    return $this->value;
  }

  /**
   * {@inheritdoc}
   */
  public static function fromInternal(mixed $value): ?BareItem {
    return match (TRUE) {
      is_scalar($value) => new BareItem($value),
      $value instanceof BareItem => $value,
      default => NULL,
    };
  }

}
