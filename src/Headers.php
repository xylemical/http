<?php

namespace Xylemical\Http;

/**
 * Provides a wrapper for headers.
 */
class Headers {

  /**
   * The headers.
   *
   * @param string[] $headers
   *   The headers.
   *
   * @return string[]
   *   The normalized headers.
   */
  public static function normalize(array $headers) {
    return array_map(function ($header) {
      return Header::normalize($header);
    }, $headers);
  }

}
