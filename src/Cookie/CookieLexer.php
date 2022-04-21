<?php

namespace Xylemical\Http\Cookie;

use Xylemical\Parser\Exception\SyntaxException;
use Xylemical\Parser\Lexer;
use Xylemical\Parser\Token;
use Xylemical\Parser\TokenStream;

/**
 * Provides the lexer for the Set-Cookie header.
 */
class CookieLexer extends Lexer {

  /**
   * Used for processing the RFC dates.
   */
  protected const DAYS = [
    'Sun',
    'Mon',
    'Tue',
    'Wed',
    'Thu',
    'Fri',
    'Sat',
  ];

  protected const MONTHS = [
    'Jan',
    'Feb',
    'Mar',
    'Apr',
    'May',
    'Jun',
    'Jul',
    'Aug',
    'Sep',
    'Oct',
    'Nov',
    'Dec',
  ];

  protected const SETTING = [
    'token',
    'octet',
    'string',
    'delimiter',
    'sp',
    'number',
  ];

  /**
   * {@inheritdoc}
   */
  public function generate(TokenStream $stream): mixed {
    return $this->doCookies($stream);
  }

  /**
   * Parse the cookies.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The token stream.
   *
   * @return \Xylemical\Http\Cookie\Cookies
   *   The cookies.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function doCookies(TokenStream $stream): Cookies {
    $cookies = [];
    while (count($stream)) {
      $stream->optional('sp');
      $cookies[] = $this->doCookie($stream);
      if (!$stream->optional('eol')) {
        break;
      }
    }
    return new Cookies($cookies);
  }

  /**
   * Parse an individual cookie.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The token stream.
   *
   * @return \Xylemical\Http\Cookie\Cookie
   *   The cookie.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function doCookie(TokenStream $stream): Cookie {
    $name = $stream->expect('token');
    $stream->expect('=');
    $value = $stream->expectOneOf(['token', 'string', 'octet']);

    $cookie = new Cookie($name->getToken(), $value->getToken());
    while ($stream->is('delimiter', ';')) {
      $stream->expect('delimiter', ';');
      $stream->expect('sp');

      $this->doCookieSetting($stream, $cookie);
    }

    return $cookie;
  }

  /**
   * Parse a cookie setting.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The token stream.
   * @param \Xylemical\Http\Cookie\Cookie $cookie
   *   The cookie.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function doCookieSetting(TokenStream $stream, Cookie $cookie): void {
    $token = $stream->expectOneOf(self::SETTING);
    if ($token->is('delimiter', ';')) {
      $stream->push($token);
      return;
    }

    switch ($token->getToken()) {
      case 'Expires':
        $this->doCookieExpires($stream, $cookie);
        break;

      case 'MaxAge':
        $this->doCookieMaxAge($stream, $cookie);
        break;

      case 'Domain':
        $this->doCookieDomain($stream, $cookie);
        break;

      case 'Path':
        $this->doCookiePath($stream, $cookie);
        break;

      case 'Secure':
        $cookie->setSecure(TRUE);
        break;

      case 'HttpOnly':
        $cookie->setHttpOnly(TRUE);
        break;

      default:
        $this->doCookieExtension($stream, $token, $cookie);
    }
  }

  /**
   * Parse an expiry date.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The token stream.
   * @param \Xylemical\Http\Cookie\Cookie $cookie
   *   The cookie.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function doCookieExpires(TokenStream $stream, Cookie $cookie): void {
    $stream->expect('delimiter', '=');

    $date = '';
    $token = $stream->expect('token', self::DAYS);
    $date .= $token->getToken();
    $token = $stream->expect('octet', ',');
    $date .= $token->getToken();
    $token = $stream->expect('sp');
    $date .= $token->getToken();
    $token = $stream->expect('number');
    if (!preg_match('/^\d{2}$/', $token->getToken())) {
      throw new SyntaxException();
    }
    $date .= $token->getToken();
    $token = $stream->expect('token', self::MONTHS);
    $date .= $token->getToken();
    $token = $stream->expect('sp');
    $date .= $token->getToken();
    $token = $stream->expect('number');
    if (!preg_match('/^\d{4}$/', $token->getToken())) {
      throw new SyntaxException();
    }
    $date .= $token->getToken();
    $token = $stream->expect('sp');
    $date .= $token->getToken();
    $token = $stream->expect('token', 'GMT');
    $date .= $token->getToken();

    $cookie->setExpires(new \DateTime($date));
  }

  /**
   * Parse a cookie max age.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The token stream.
   * @param \Xylemical\Http\Cookie\Cookie $cookie
   *   The cookie.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function doCookieMaxAge(TokenStream $stream, Cookie $cookie): void {
    $stream->expect('delimiter', '=');
    $age = $stream->expect('number');
    $cookie->setMaxAge(intval($age));
  }

  /**
   * @param \Xylemical\Parser\TokenStream $stream
   * @param \Xylemical\Http\Cookie\Cookie $cookie
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function doCookieDomain(TokenStream $stream, Cookie $cookie): void {
    $stream->expect('delimiter', '=');
    // TODO: This needs something of the URI validators.
  }

  /**
   * Parsse a cookie path.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The token stream.
   * @param \Xylemical\Http\Cookie\Cookie $cookie
   *   The cookie.
   *
   * @throws \Xylemical\Parser\Exception\SyntaxException
   */
  protected function doCookiePath(TokenStream $stream, Cookie $cookie): void {
    $stream->expect('delimiter', '=');
    if (!($value = $this->getValue($stream))) {
      throw new SyntaxException();
    }
    $cookie->setPath($value);
  }

  /**
   * Parses a cookie extension string.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The token stream.
   * @param \Xylemical\Parser\Token $token
   *   The original token.
   * @param \Xylemical\Http\Cookie\Cookie $cookie
   *   The cookie.
   *
   * @return void
   */
  protected function doCookieExtension(TokenStream $stream, Token $token, Cookie $cookie): void {
    $extension = $token->getToken();
    $extension .= $this->getValue($stream);
    $cookie->addExtension($extension);
  }

  /**
   * Get a value from the stream.
   *
   * @param \Xylemical\Parser\TokenStream $stream
   *   The stream.
   *
   * @return string
   *   The value.
   */
  protected function getValue(TokenStream $stream): string {
    $value = '';
    while ($token = $stream->optionalOneOf(self::SETTING)) {
      if ($token->is('delimiter', ';')) {
        $stream->push($token);
        break;
      }

      $value .= $token->getToken();
    }
    return $value;
  }

}
