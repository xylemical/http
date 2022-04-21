<?php

namespace Xylemical\Http\StructuredField;

/**
 * Tests \Xylemical\Http\Parameters.
 */
class ParametersTest extends AbstractTestCase {

  /**
   * {@inheritdoc}
   */
  protected array $filenames = [
    'param-dict.json',
    'param-list.json',
    'param-listlist.json',
  ];

  /**
   * Provides test data to confirm toInternal works as expected.
   *
   * @return array
   *   Load the test data.
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
      [['test'], TRUE],
      [['a' => 1.0], TRUE],
      [(object) ['test' => 'value'], FALSE],
    ];
  }

  /**
   * Test the toInternal()/fromInternal()
   *
   * @dataProvider providerTestInternal
   */
  public function testInternal(mixed $value, mixed $success): void {
    $item = Parameters::fromInternal($value);
    if ($success) {
      $this->assertEquals($value, $item->toInternal());
    }
    else {
      $this->assertEquals(0, \count($item));
    }
  }

  /**
   * Test the sanity.
   */
  public function testSanity(): void {
    $parameters = new Parameters();
    $this->assertEquals(0, \count($parameters));
    $this->assertFalse($parameters->offsetExists('q'));
    $this->assertNull($parameters->offsetGet('q'));
    $this->assertEquals([], \iterator_to_array($parameters->getIterator()));
    $this->assertEquals([], $parameters->all());
    $this->assertFalse($parameters->has('q'));
    $this->assertNull($parameters->get('q'));

    $parameters->set('q', TRUE);
    $this->assertEquals(1, \count($parameters));
    $this->assertTrue($parameters->offsetExists('q'));
    $this->assertEquals(TRUE, $parameters->offsetGet('q'));
    $this->assertEquals(['q' => TRUE], \iterator_to_array($parameters->getIterator()));
    $this->assertEquals(['q' => BareItem::fromInternal(TRUE)], $parameters->all());
    $this->assertTrue($parameters->has('q'));
    $this->assertEquals(BareItem::fromInternal(TRUE), $parameters->get('q'));

    $parameters->set('t', 'tree');
    $this->assertEquals(2, \count($parameters));
    $this->assertEquals([
      'q' => TRUE,
      't' => 'tree',
    ], \iterator_to_array($parameters->getIterator()));
    $this->assertEquals([
      'q' => BareItem::fromInternal(TRUE),
      't' => BareItem::fromInternal('tree'),
    ], $parameters->all());

    $parameters->remove('t');
    $this->assertEquals(['q' => TRUE], \iterator_to_array($parameters->getIterator()));

    $parameters->offsetSet('q', FALSE);
    $this->assertEquals(['q' => FALSE], \iterator_to_array($parameters->getIterator()));

    $parameters->clear();
    $this->assertEquals([], \iterator_to_array($parameters->getIterator()));

    $parameters->set('q', TRUE);
    $parameters->offsetUnset('q');
    $this->assertEquals([], \iterator_to_array($parameters->getIterator()));
  }

}
