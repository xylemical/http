<?php

namespace Xylemical\Http\Temp;

use Xylemical\Http\Exception\SyntaxException;
use Xylemical\Http\StructuredField\StructuredField;
use Xylemical\Http\StructuredField\StructuredFieldInterface;

/**
 * Provides support for older headers of HTTP-Date.
 *
 * Not part of the RFC8941 specification, despite using the interfaces.
 *
 * @see https://datatracker.ietf.org/doc/html/rfc7231#section-7.1.1.1
 */
final class Date extends StructuredField {

  /**
   * The date value.
   *
   * @var \DateTime
   */
  protected \DateTime $date;

  /**
   * The regex used for RFC 7231 dates.
   */
  protected const RFC7231_REGEX = '(?Mon|Tue|Wed|Thu|Fri|Sat|Sun), \d{2} (?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) \d{4} \d{2}:\d{2}:\d{2} GMT';

  /**
   * The regex used for RFC 850 dates.
   */
  protected const RFC850_REGEX = '(?:Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday) \d{2}-\d{2}-\d{4} \d{2}:\d{2}:\d{2} GMT';

  /**
   * Regex used by the obsolete date format.
   */
  protected const OBS_REGEX = '(?Mon|Tue|Wed|Thu|Fri|Sat|Sun) (?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) (?\d{2}| \d) \d{2}:\d{2}:\d{2} \d{4}';

  /**
   * Date constructor.
   *
   * @param \DateTime $date
   *   The date.
   */
  public function __construct(\DateTime $date) {
    $this->date = $date;
  }

  /**
   * Get the datetime.
   *
   * @return \DateTime
   *   The datetime.
   */
  public function getDate(): \DateTime {
    return $this->date;
  }

  /**
   * Set the datetime.
   *
   * @param \DateTime $date
   *   The datetime.
   *
   * @return $this
   */
  public function setDate(\DateTime $date): static {
    $this->date = $date;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function parse(string &$input): ?StructuredFieldInterface {
    try {
      // Parse IMF-fixdate
      if (preg_match('#^' . static::RFC7231_REGEX . '#', $input, $match)) {
        $input = substr($input, strlen($match[0]));
        return new Date(new \DateTime($match[0]));
      }
      if (preg_match('#^' . static::RFC850_REGEX . '#', $input, $match)) {
        $input = substr($input, strlen($match[0]));
        return new Date(new \DateTime($match[0]));
      }
      if (preg_match('#^' . static::OBS_REGEX . '#', $input, $match)) {
        return new Date(new \DateTime($match[0], new \DateTimeZone('GMT')));
      }
      return NULL;
    }
    catch (\Exception $e) {
      throw new SyntaxException($e->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function serialize(): string {
    $this->date->setTimezone(new \DateTimeZone('GMT'));
    return $this->date->format('D, d M Y H:i:s e');
  }

  /**
   * {@inheritdoc}
   */
  public function toInternal(): mixed {
    return $this->date->getTimestamp();
  }

  /**
   * {@inheritdoc}
   */
  public static function fromInternal(mixed $value): ?StructuredFieldInterface {
    return match (TRUE) {
      is_int($value) => new Date(new \DateTime("@{$value}")),
      is_string($value) => new Date(new \DateTime($value)),
      $value instanceof \DateTime => new Date($value),
      default => NULL,
    };
  }

}
