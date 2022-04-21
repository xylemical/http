<?php

namespace Xylemical\Http\StructuredField;

/**
 * Tests \Xylemical\Http\Key.
 */
class KeyTest extends AbstractTestCase {

  /**
   * {@inheritdoc}
   */
  protected array $filenames = [
    'key-generated.json',
    'serialisation-tests/key-generated.json',
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
    $item = Key::fromInternal($value);
    if ($success) {
      $this->assertEquals($value, $item->toInternal());
    }
    else {
      $this->assertNull($item);
    }
  }

  /**
   * Test the sanity.
   */
  public function testSanity(): void {
    $key = new Key('test');
    $this->assertEquals('test', $key->getValue());
  }

}
