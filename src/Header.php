<?php

namespace Xylemical\Http;

use Exception;
use Xylemical\Parser\Parser;

/**
 * Provides the header handler.
 */
final class Header {

  /**
   * The header name.
   *
   * @var string
   */
  protected string $name;

  /**
   * The header item.
   *
   * @var mixed
   */
  protected mixed $item;

  /**
   * Header constructor.
   *
   * @param string $name
   *   The name.
   * @param mixed|null $item
   *   The value.
   */
  public function __construct(string $name, mixed $item = NULL) {
    $this->name = $name;
    $this->item = $item;
  }

  /**
   * Get the header name.
   *
   * @return string
   *   The name.
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * Get the header item.
   *
   * @return mixed
   *   The header item.
   */
  public function getItem(): mixed {
    return $this->item;
  }

  /**
   * Normalizes a header name.
   *
   * @param string $header
   *   The name.
   * @param \Xylemical\Http\ProviderInterface|null $provider
   *   The provider.
   *
   * @return string
   *   The normalized header name.
   */
  public static function normalize(string $header, ?ProviderInterface $provider = NULL): string {
    $name = strtolower($header);
    if ($provider->applies($name)) {
      return $provider->normalize($header);
    }
    return implode('-', array_map('ucfirst', explode('-', $name)));
  }

  /**
   * Parse header lines into this header object.
   *
   * @param string $name
   *   The name.
   * @param \Xylemical\Http\ProviderInterface $provider
   *   The provider.
   *
   * @return \Xylemical\Http\Header
   *   The header.
   */
  public static function parse(string $name, array $values, ProviderInterface $provider): Header {
    $header = NULL;
    try {
      $name = strtolower($name);
      if (!$provider->applies($name)) {
        throw new Exception();
      }

      $line = $provider->getLine($name, $values);
      $parser = new Parser(
        $provider->getTokenizer($name),
        $provider->getLexer($name)
      );
      return new Header($parser->parse($line));
    }
    catch (Exception) {
    }
    return new Header($header);
  }

  /**
   * Generate the header lines from the header object.
   *
   * @param \Xylemical\Http\Header $header
   *   The header.
   * @param \Xylemical\Http\ProviderInterface $provider
   *   The provider.
   *
   * @return string[]
   *   The header lines.
   */
  public static function generate(Header $header, ProviderInterface $provider): array {
    $values = [];
    try {
      $name = strtolower($header->getName());
      if ($provider->applies($name)) {
        throw new Exception();
      }

      return $provider->getValue($name, $provider->serialize($name, $header->getItem()));
    }
    catch (Exception) {
    }
    return $values;
  }

}
