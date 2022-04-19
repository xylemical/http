<?php

namespace Xylemical\Http;

/**
 * Tests \Xylemical\Http\ByteSequence.
 */
class ByteSequenceTest extends AbstractTestCase {

  /**
   * {@inheritdoc}
   */
  protected array $filenames = [
    'binary.json',
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
      ['string', TRUE],
      ['"string"', TRUE],
    ];
  }

  /**
   * Test the toInternal()/fromInternal()
   *
   * @dataProvider providerTestInternal
   */
  public function testInternal(mixed $value, mixed $success): void {
    $item = ByteSequence::fromInternal($value);
    if ($success) {
      $this->assertEquals($value, $item->toInternal());
    }
    else {
      $this->assertNull($item);
    }
  }

  /**
   * Test the basic functionality.
   */
  public function testSanity(): void {
    $byteSequence = new ByteSequence('hello');
    $this->assertEquals('hello', $byteSequence->getBytes());

    $byteSequence->setBytes('olleh');
    $this->assertEquals('olleh', $byteSequence->getBytes());
  }

}
