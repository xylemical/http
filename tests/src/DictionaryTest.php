<?php

namespace Xylemical\Http;

/**
 * Tests \Xylemical\Http\Dictionary.
 */
class DictionaryTest extends AbstractTestCase {

  /**
   * {@inheritdoc}
   */
  protected array $filenames = [
    'dictionary.json',
    'large-generated.json',
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
      [['test'], [['test']]],
      [['a' => 0], ['a' => [0]]],
      [(object) ['test' => 'value'], FALSE],
    ];
  }

  /**
   * Test the toInternal()/fromInternal()
   *
   * @dataProvider providerTestInternal
   */
  public function testInternal(mixed $value, mixed $success): void {
    $item = Dictionary::fromInternal($value);
    if ($success) {
      $this->assertEquals($success, $item->toInternal());
    }
    else {
      $this->assertNull($item);
    }
  }

  /**
   * Test the sanity.
   */
  public function testSanity(): void {
    $dictionary = new Dictionary();
    $this->assertEquals(0, count($dictionary));
    $this->assertFalse($dictionary->offsetExists('q'));
    $this->assertNull($dictionary->offsetGet('q'));
    $this->assertEquals([], iterator_to_array($dictionary->getIterator()));
    $this->assertEquals([], $dictionary->all());
    $this->assertFalse($dictionary->has('q'));
    $this->assertNull($dictionary->get('q'));

    $dictionary->set('q', TRUE);
    $this->assertEquals(1, count($dictionary));
    $this->assertTrue($dictionary->offsetExists('q'));
    $this->assertEquals([TRUE], $dictionary->offsetGet('q'));
    $this->assertEquals(['q' => [TRUE]], iterator_to_array($dictionary->getIterator()));
    $this->assertEquals(['q' => Item::fromInternal(TRUE)], $dictionary->all());
    $this->assertTrue($dictionary->has('q'));
    $this->assertEquals(Item::fromInternal(TRUE), $dictionary->get('q'));

    $dictionary->set('t', 'tree');
    $this->assertEquals(2, count($dictionary));
    $this->assertEquals([
      'q' => [TRUE],
      't' => ['tree'],
    ], iterator_to_array($dictionary->getIterator()));
    $this->assertEquals([
      'q' => Item::fromInternal(TRUE),
      't' => Item::fromInternal('tree'),
    ], $dictionary->all());

    $dictionary->remove('t');
    $this->assertEquals(['q' => [TRUE]], iterator_to_array($dictionary->getIterator()));

    $dictionary->offsetSet('q', FALSE);
    $this->assertEquals(['q' => [FALSE]], iterator_to_array($dictionary->getIterator()));

    $dictionary->clear();
    $this->assertEquals([], iterator_to_array($dictionary->getIterator()));

    $dictionary->set('q', TRUE);
    $dictionary->offsetUnset('q');
    $this->assertEquals([], iterator_to_array($dictionary->getIterator()));
  }

}
