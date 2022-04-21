<?php

namespace Xylemical\Http\Provider;

use Xylemical\Http\ProviderInterface;

/**
 * Provides the HTTP Headers as defined in RFC 7230
 */
class HttpProvider implements ProviderInterface {

  protected const HEADERS = [
    // RFC 6265 HTTP State Management Mechanism
    'Set-Cookie' => [
      'type' => 'dictionary',
    ],

    // RFC 6266 Use of the Content-Disposition Header Field
    'Content-Disposition' => [
      'type' => 'dictionary',
    ],

    // RFC 7230 Message Syntax and Routing
    // @see https://datatracker.ietf.org/doc/html/rfc7230
    [
      'Host',
      'Via',
      'Connection',
      'Upgrade',
      'Transfer-Encoding',
      'Content-Length',
      'Trailer',
      'TE',
    ],

    // RFC 7231 Semantics and Content
    // @see https://datatracker.ietf.org/doc/html/rfc7231
    'Content-Type' => [
      'type' => 'item',
    ],
    'Content-Encoding' => [
      'type' => 'list',
    ],
    'Content-Language' => [
      'type' => 'list',
    ],
    'Content-Location' => [
      'type' => 'item', // TODO: uri (including relative url)
    ],
    'Date' => [
      'type' => 'date',
    ],
    'Expect' => [
      'type' => 'item',
      'enum' => ['100-continue'],
    ],
    'Max-Forwards' => [
      'type' => 'item',
    ],
    'Accept' => [
      'type' => 'list',
    ],
    'Accept-Charset' => [
      'type' => 'list',
    ],
    'Accept-Encoding' => [
      'type' => 'list',
    ],
    'Accept-Language' => [
      'type' => 'list',
    ],
    'Allow' => [
      'type' => 'list',
    ],
    'From' => [
      'type' => 'item', // TODO: email
    ],
    'Location' => [
      'type' => 'item', // TODO: url
    ],
    'Referer' => [
      'type' => 'item', // TODO: uri
    ],
    'Retry-After' => [
      'type' => 'date', // TODO: date/seconds
    ],
    'Server' => [
      'type' => '', // TODO: uri
    ],
    'User-Agent' => [
      'type' => 'space-list',
    ],
    'Vary' => [
      'type' => 'list',
    ],

    // RFC 7232 Conditional Requests.
    // @see https://datatracker.ietf.org/doc/html/rfc7232
    'Last-Modified' => [
      'type' => 'date',
    ],
    'ETag' => [
      'type' => 'item',
      // TODO: This should probably be an 'entity-tag' type. @see https://datatracker.ietf.org/doc/html/rfc7232#section-2.3
    ],
    'If-Match' => [
      'type' => '',
      // TODO: This should probably be an 'entity-tag' type. (but is a list, and matches *)
    ],
    'If-None-Match' => [
      'type' => '',
      // TODO: This should probably be an 'entity-tag' type. (this is a list, and matches *)
    ],
    'If-Modified-Since' => [
      'type' => 'date',
    ],
    'If-Unmodified-Since' => [
      'type' => 'date',
    ],

    // RFC 7233 Range Requests.
    // @see https://datatracker.ietf.org/doc/html/rfc7233
    'Accept-Ranges' => [
      'type' => 'dictionary',
    ],
    'Range' => [
      'type' => 'item',
    ],
    'If-Range' => [
      'type' => 'item', // TODO: This should probably be an 'entity-tag' type.
    ],
    'Content-Range' => [
      'type' => 'space-list',
    ],

    // RFC 7234 Caching
    // @see https://datatracker.ietf.org/doc/html/rfc7234
    'Age' => [
      'type' => 'item',
    ],
    'Cache-Control' => [
      'type' => 'dictionary',
    ],
    'Expires' => [
      'type' => 'date',
    ],
    'Pragma' => [
      'type' => 'dictionary',
    ],
    'Warning' => [
      'type' => 'list', // TODO: Each item also contains a space.
    ],

    // RFC 7235 Authentication
    // @see https://datatracker.ietf.org/doc/html/rfc7235
    'Authorization' => [
      'type' => 'dictionary',
    ],
    'Proxy-Authenticate' => [
      'type' => 'list',
    ],
    'Proxy-Authorization' => [
      'type' => 'dictionary',
    ],
    'WWW-Authenticate' => [
      'type' => 'list',
    ],

    // RFC 7239 Forwarded HTTP Extension
    // @see https://datatracker.ietf.org/doc/html/rfc7239
    'Forwarded' => [
      'type' => 'dictionary',
    ],

    // RFC 7240 Prefer Header for HTTP
    // @see https://datatracker.ietf.org/doc/html/rfc7240
    'Prefer' => [
      'type' => 'dictionary',
    ],

    // RFC 7838 HTTP Alternative Services
    // @see https://datatracker.ietf.org/doc/html/rfc7838
    'Alt-Svc' => [
      'type' => 'dictionary',
    ],
    'Alt-Used' => [
      'type' => 'item',
    ],

    // RFC 8288 Web Linking
    // @see https://datatracker.ietf.org/doc/html/rfc8288
    'Link' => [
      'type' => 'list', // TODO: special list format.
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function getHeaders(): array {

  }

  /**
   * {@inheritdoc}
   */
  public function getType(string $header): string {
    // TODO: Implement getType() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getParsers(): array {
    // TODO: Implement getParsers() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getGenerators(): array {
    // TODO: Implement getGenerators() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getLine(string $header, array $values): string {
    // TODO: Implement getHeaderLine() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getValue(string $header, string $value): array {
    // TODO: Implement getHeader() method.
  }

}