<?php

namespace Xylemical\Http\Cookie;

use DateTime;
use Xylemical\Parser\Exception\SyntaxException;

/**
 * Provides a standard cookie.
 */
class Cookie implements \Stringable {

  /**
   * The cookie name.
   *
   * @var string
   */
  protected string $name = '';

  /**
   * The cookie value.
   *
   * @var string
   */
  protected string $value = '';

  /**
   * The expires time or timestamp.
   *
   * @var DateTime|null
   */
  protected ?DateTime $expires = NULL;

  /**
   * The max age.
   *
   * @var int
   */
  protected int $maxAge = 0;

  /**
   * The path.
   *
   * @var string
   */
  protected string $path;

  /**
   * Flag to indicate the cookie is secure.
   *
   * @var bool
   */
  protected bool $secure = TRUE;

  /**
   * Flag to indicate the cookie is http only.
   *
   * @var bool
   */
  protected bool $httpOnly = FALSE;

  /**
   * The extensions to the cookie.
   *
   * @var string[]
   */
  protected array $extensions = [];

  /**
   * Cookie constructor.
   *
   * @param string $name
   *   The name.
   * @param string $value
   *   The value.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  public function __construct(string $name, string $value) {
    if (!CookieTokenizer::isCookieName($name)) {
      throw new SyntaxException();
    }
    $this->setValue($value);
  }

  /**
   * Set the cookie value.
   *
   * @param string $value
   *   The value for the cookie.
   *
   * @return $this
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  public function setValue(string $value): static {
    if (!CookieTokenizer::isCookieValue($value)) {
      throw new SyntaxException();
    }
    $this->value = $value;
    return $this;
  }

  /**
   * Set the expiry date.
   *
   * @param \DateTime|null $date
   *   The date.
   *
   * @return $this
   */
  public function setExpires(?DateTime $date): static {
    $this->expires = $date;
    return $this;
  }

  public function setMaxAge(int $age): static {
    $this->maxAge = $age;
    return $this;
  }

  public function setPath(string $path): static {
    if ($this->checkValue($path)) {
      $this->path = $path;
    }
    return $this;
  }

  public function setSecure(bool $flag): static {
    $this->secure = $flag;
    return $this;
  }

  public function setHttpOnly(bool $flag): static {
    $this->httpOnly = $flag;
    return $this;
  }

  /**
   * Add an extension to the cookie.
   *
   * @param string $extension
   *   The cookie extension.
   *
   * @return $this
   */
  public function addExtension(string $extension): static {
    if ($this->checkValue($extension)) {
      $this->extensions[] = $extension;
    }
    return $this;
  }

  /**
   * Check the value is considered valid for a cookie.
   *
   * @param string $value
   *   The value.
   *
   * @return bool
   *   The result.
   */
  protected function checkValue(string $value): bool {
    return preg_match('/^[\x20-\x3a\x3c-\x7f]+$/', $value);
  }

  public function __toString(): string {
    // TODO: Implement __toString() method.
  }

}
