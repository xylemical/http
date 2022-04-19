<?php

namespace Xylemical\Http;

/**
 * Tests \Xylemical\Http\BareItem.
 */
class BareItemTest extends AbstractTestCase {

  /**
   * {@inheritdoc}
   */
  protected array $filenames = [
    'boolean.json',
    'string.json',
    'string-generated.json',
    'number.json',
    'number-generated.json',
    'token.json',
    'token-generated.json',
    'serialisation-tests/number.json',
    'serialisation-tests/string-generated.json',
    'serialisation-tests/token-generated.json',
  ];

  /**
   * Provides test data to confirm toInternal works as expected.
   *
   * @return array
   *   The test data
   */
  public function providerTestInternal(): array {
    return [
      [NULL, FALSE],
      [TRUE, TRUE],
      [FALSE, TRUE],
      [0, TRUE],
      [0.1, TRUE],
      ['string', TRUE],
      ['"string"', TRUE],
      [['test'], FALSE],
      [(object) ['test' => 'value'], FALSE],
    ];
  }

  /**
   * Test the toInternal()/fromInternal()
   *
   * @dataProvider providerTestInternal
   */
  public function testInternal(mixed $value, mixed $success): void {
    $item = BareItem::fromInternal($value);
    if ($success) {
      $this->assertEquals($value, $item->toInternal());
    }
    else {
      $this->assertNull($item);
    }
  }

}
