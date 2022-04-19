<?php

namespace Xylemical\Http;

/**
 * Tests \Xylemical\Http\Item.
 */
class ItemTest extends AbstractTestCase {

  /**
   * {@inheritdoc}
   */
  protected array $filenames = [
    'item.json',
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
      [TRUE, FALSE],
      [FALSE, FALSE],
      [0, FALSE],
      [0.1, FALSE],
      ['string', FALSE],
      ['"string"', FALSE],
      [['@attributes' => ['q' => 1.0], 'value'], TRUE],
      [['a', 'b', 'c'], FALSE],
      [Item::fromInternal('a'), TRUE],
    ];
  }

  /**
   * Test the toInternal()/fromInternal()
   *
   * @dataProvider providerTestInternal
   */
  public function testInternal(mixed $value, mixed $success): void {
    $item = Item::fromInternal($value);
    if ($success) {
      if ($value instanceof Item) {
        $this->assertEquals($value, $item);
      }
      else {
        $this->assertEquals($value, $item->toInternal());
      }
    }
    elseif ($item instanceof InnerSequence) {
      foreach ($item as $k => $v) {
        $this->assertEquals($value[$k], $v[0]);
      }
    }
    elseif ($item instanceof Item) {
      $this->assertEquals($value, $item->getValue()->toInternal());
    }
    else {
      $this->assertNull($item);
    }
  }

}
