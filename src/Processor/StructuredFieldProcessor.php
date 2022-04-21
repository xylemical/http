<?php

namespace Xylemical\Http\Processor;

use Xylemical\Http\AbstractProcessor;
use Xylemical\Http\HeaderItemInterface;
use Xylemical\Http\ProcessorInterface;

/**
 * Provides patterns for structured fields.
 */
final class StructuredFieldProcessor extends AbstractProcessor  {

  /**
   * Patterns used in the processor.
   */
  protected const PATTERNS = [
    'sf-number' => '',
    'sf-integer' => '',
    'sf-boolean' => '',
    'sf-string' => '',
    'sf-token' => '',
    'sf-bare-item' => '',
    'sf-inner-list' => '',
    'sf-item' => '',
    'sf-list' => '',
    'sf-dictionary' => '',
  ];

  public function generate(string $type, HeaderItemInterface $item): string {
    // TODO: Implement generate() method.
  }

  protected function getItem(string $type, array &$match): HeaderItemInterface {
    // TODO: Implement getItem() method.
  }

  protected function buildItem(string $type, array $pattern, array &$match): HeaderItemInterface {
    // TODO: Implement buildItem() method.
  }


}
