<?php

namespace Xylemical\Http\Provider;

class ContentSecurityPolicyProvider {

  protected const HEADERS = [
    // Content Security Policy
    // @see https://www.w3.org/TR/CSP3/
    'Content-Security-Policy' => [
      'type' => 'csp-list',
    ],
    'Content-Security-Policy-Report-Only' => [
      'type' => 'csp-list',
    ],
  ];

}