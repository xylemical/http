<?php

namespace Xylemical\Http;

/**
 * Tests \Xylemical\Http\Field.
 */
class FieldTest extends AbstractTestCase {

  /**
   * {@inheritdoc}
   */
  protected array $filenames = [
    'examples.json',
  ];

  /**
   * Test invalid data.
   */
  public function testInvalidData(): void {
    $this->assertNull(Field::parse('dummy', ''));
  }

}
