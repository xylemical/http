<?php

namespace Xylemical\Http;

/**
 * Tests \Xylemical\Http\Sequence.
 */
class SequenceTest extends AbstractTestCase {

  /**
   * {@inheritdoc}
   */
  protected array $filenames = [
    'list.json',
  ];

  /**
   * Provides test data to confirm toInternal works as expected.
   *
   * @return array
   *   The test data.
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
      [['a' => 0], FALSE],
      [(object) ['test' => 'value'], FALSE],
    ];
  }

  /**
   * Test the toInternal()/fromInternal()
   *
   * @dataProvider providerTestInternal
   */
  public function testInternal(mixed $value, mixed $success): void {
    $item = Sequence::fromInternal($value);
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
    $sequence = new Sequence();
    $this->assertEquals(0, count($sequence));
    $this->assertFalse($sequence->offsetExists(0));
    $this->assertNull($sequence->offsetGet(0));
    $this->assertEquals([], iterator_to_array($sequence->getIterator()));
    $this->assertEquals([], $sequence->all());
    $this->assertFalse($sequence->has(0));
    $this->assertNull($sequence->get(0));

    $sequence->add('q');
    $this->assertEquals(1, count($sequence));
    $this->assertTrue($sequence->offsetExists(0));
    $this->assertEquals(['q'], $sequence->offsetGet(0));
    $this->assertEquals([['q']], iterator_to_array($sequence->getIterator()));
    $this->assertEquals([Item::fromInternal('q')], $sequence->all());
    $this->assertTrue($sequence->has(0));
    $this->assertEquals(Item::fromInternal('q'), $sequence->get(0));

    $sequence->add('t');
    $this->assertEquals(2, count($sequence));
    $this->assertEquals([
      ['q'],
      ['t'],
    ], iterator_to_array($sequence->getIterator()));
    $this->assertEquals([
      Item::fromInternal('q'),
      Item::fromInternal('t'),
    ], $sequence->all());

    $sequence->remove(0);
    $this->assertEquals([['t']], iterator_to_array($sequence->getIterator()));

    $sequence->offsetSet(0, FALSE);
    $this->assertEquals([[FALSE]], iterator_to_array($sequence->getIterator()));

    $sequence->clear();
    $this->assertEquals([], iterator_to_array($sequence->getIterator()));

    $sequence->add('q');
    $sequence->offsetUnset(0);
    $this->assertEquals([], iterator_to_array($sequence->getIterator()));
  }

}
