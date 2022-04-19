<?php

namespace Xylemical\Http;

use Base32\Base32;
use PHPUnit\Framework\TestCase;

/**
 * Provides an abstract test case for parsing and serialization of data.
 */
abstract class AbstractTestCase extends TestCase {

  /**
   * The filenames containing the test data.
   *
   * @var string[]
   */
  protected array $filenames;

  /**
   * Provides the test cases for parsing.
   *
   * @return \Xylemical\Http\StructuredFieldTestCase[][]
   *   The test cases.
   */
  public function providerTestParse() {
    $tests = [];
    foreach ($this->filenames as $filename) {
      $path = __DIR__ . '/../../vendor/httpwg/structured-field-tests/' . $filename;
      $tests = array_merge($tests, array_map(function ($test) {
        return [$test];
      }, StructuredFieldTestCase::load($path)));
    }
    return $tests;
  }

  /**
   * Build a bare item.
   *
   * @param mixed $construct
   *   The bare item value.
   *
   * @return \Xylemical\Http\StructuredFieldItemInterface|null
   *   The bare item.
   */
  protected function buildBare(mixed $construct): ?StructuredFieldItemInterface {
    if (is_array($construct)) {
      if (!empty($construct['__type'])) {
        return match ($construct['__type']) {
          'binary' => new ByteSequence(Base32::decode($construct['value'])),
          default => new BareItem($construct['value']),
        };
      }

      throw new \Exception('Unidentified array for bare item');
    }
    return BareItem::fromInternal(is_string($construct) ? "\"{$construct}\"" : $construct);
  }

  /**
   * Build an item.
   *
   * @param mixed $construct
   *   The value.
   *
   * @return \Xylemical\Http\StructuredFieldSequenceInterface|null
   *   The item or NULL.
   */
  protected function buildItem(mixed $construct): ?StructuredFieldSequenceInterface {
    $parameters = [];
    foreach ($construct[1] as $value) {
      $parameters[$value[0]] = $this->buildBare($value[1]);
    }

    if (!empty($construct[0]['__type'])) {
      $item = $this->buildBare($construct[0]);
      return new Item($item, new Parameters($parameters));
    }

    if (is_array($construct[0])) {
      $values = [];
      foreach ($construct[0] as $key => $value) {
        if ($item = $this->buildItem($value)) {
          $values[$key] = $item;
        }
      }
      return new InnerSequence($values, new Parameters($parameters));
    }

    $item = $this->buildBare($construct[0]);
    return new Item($item, new Parameters($parameters));
  }

  /**
   * Build the expected result.
   *
   * @param string $type
   *   The type.
   * @param mixed $construct
   *   The construct.
   *
   * @return \Xylemical\Http\StructuredFieldInterface|null
   *   The result.
   */
  protected function build(string $type, mixed $construct): ?StructuredFieldInterface {
    if ($type === 'item') {
      return $this->buildItem($construct);
    }

    if ($type === 'list') {
      $values = [];
      foreach ($construct as $value) {
        if ($item = $this->buildItem($value)) {
          $values[] = $item;
        }
      }
      return new Sequence($values);
    }

    $values = [];
    foreach ($construct as $value) {
      if ($item = $this->buildItem($value[1])) {
        $values[$value[0]] = $item;
      }
    }

    return new Dictionary($values);
  }

  /**
   * Test the parse functionality.
   *
   * @dataProvider providerTestParse
   */
  public function testParse(StructuredFieldTestCase $case): void {
    $name = $case->getName();

    if ($raw = $case->getRaw()) {
      $raw = Field::parse($case->getHeaderType(), implode(', ', $raw));
      $rawString = (string) $raw;
    }

    $expected = $case->getExpected();
    if (!$case->mustFail() || $expected) {
      $expected = $this->build($case->getHeaderType(), $expected);
      $expectedString = (string) $expected;
    }

    if (empty($result) && $case->canFail()) {
      $this->assertTrue(TRUE, "{$name} can fail.");
      return;
    }

    if ($case->mustFail()) {
      $expected = empty($expected) || empty($expectedString);
      $raw = empty($raw) || empty($rawString);
      $this->assertTrue($expected, "{$name} must fail expected.");
      $this->assertTrue($raw, "{$name} must fail raw.");
      return;
    }

    if (!empty($raw) && !empty($expected)) {
      $this->assertEquals($raw, $expected, "{$name} raw == expected");
    }
    if (!empty($rawString) && !empty($expectedString)) {
      $this->assertEquals($rawString, $expectedString, "{$name} raw string == expected string");
    }

    // Check against canonical.
    if ($canonical = $case->getCanonical()) {
      $canonical = implode(', ', $canonical);
      if (!empty($expected)) {
        $this->assertEquals($canonical, $expectedString ?? '', "{$name} expected == canonical");
      }
      if (!empty($result)) {
        $this->assertEquals($canonical, $rawString ?? '', "{$name} raw == canonical");
      }
    }
  }

}
