<?php

namespace Xylemical\Http\Provider;

class CorsProvider {

  protected const HEADERS = [
    // CORS Headers
    // @see https://fetch.spec.whatwg.org/
    'Access-Control-Allow-Credentials' => [
      'type' => 'item',
    ],
    'Access-Control-Allow-Headers' => [
      'type' => 'list',
    ],
    'Access-Control-Allow-Methods' => [
      'type' => 'list',
    ],
    'Access-Control-Allow-Origin' => [
      'type' => 'item',
    ],
    'Access-Control-Expose-Headers' => [
      'type' => 'list',
    ],
    'Access-Control-Max-Age' => [
      'type' => 'item',
    ],
    'Access-Control-Request-Method' => [
      'type' => 'item',
    ],
    'Access-Control-Request-Headers' => [
      'type' => 'item',
    ],
    'Origin' => [
      'type' => 'item', // TODO: uri
    ],
  ];

}