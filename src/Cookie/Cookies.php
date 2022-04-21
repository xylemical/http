<?php

namespace Xylemical\Http\Cookie;

/**
 * Provides support for multiple cookies.
 */
class Cookies implements \Stringable {

  /**
   * The cookies.
   *
   * @var \Xylemical\Http\Cookie\Cookie[]
   */
  protected array $cookies = [];

  /**
   * {@inheritdoc}
   */
  public function __toString(): string {
    return implode("\n", $this->cookies);
  }

}
