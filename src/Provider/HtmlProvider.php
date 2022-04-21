<?php

namespace Xylemical\Http\Provider;

class HtmlProvider {

  protected const HEADERS = [

    // HTML Specification
    // @see https://html.spec.whatwg.org/multipage/
    'Last-Event-Id' => [
      'type' => 'item',
    ],
    'Ping-From' => [
      'type' => 'item',
    ],
    'Ping-To' => [
      'type' => 'item',
    ],
    'Refresh' => [
      'type' => 'item',
    ],
    'Cross-Origin-Embedder-Policy' => [
      'type' => 'item',
    ],
    'Cross-Origin-Embedder-Policy-Report-Only' => [
      'type' => 'item',
    ],
    'Cross-Origin-Opener-Policy' => [
      'type' => 'item',
    ],
    'Cross-Origin-Opener-Policy-Report-Only' => [
      'type' => 'item',
    ],
    'Origin-Agent-Cluster' => [
      'type' => 'item',
    ],
    'X-Frame-Options' => [
      'type' => 'item',
    ],

  ];

}