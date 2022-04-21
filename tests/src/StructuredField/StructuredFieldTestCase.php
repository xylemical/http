<?php

namespace Xylemical\Http\StructuredField;

/**
 * Provides a test using the httpwg/structured-field-tests repository.
 */
class StructuredFieldTestCase {

  /**
   * The name.
   *
   * @var string
   */
  protected string $name;

  /**
   * The raw values.
   *
   * @var array
   */
  protected array $raw;

  /**
   * The header type.
   *
   * @var string
   */
  protected string $headerType;

  /**
   * The expected result.
   *
   * @var mixed
   */
  protected mixed $expected;

  /**
   * The test must fail.
   *
   * @var bool
   */
  protected bool $mustFail;

  /**
   * The test can fail.
   *
   * @var bool
   */
  protected bool $canFail;

  /**
   * The canonical items.
   *
   * @var array
   */
  protected array $canonical;

  /**
   * StructuredFieldTestCase constructor.
   *
   * @param string $name
   *   The name.
   * @param array $raw
   *   The raw values.
   * @param string $header_type
   *   The header type.
   * @param mixed $expected
   *   The expected result.
   * @param bool $must_fail
   *   The test must fail.
   * @param bool $can_fail
   *   The test can fail.
   * @param array $canonical
   *   The canoncial values.
   */
  public function __construct(string $name, array $raw, string $header_type, mixed $expected, bool $must_fail, bool $can_fail, array $canonical) {
    $this->name = $name;
    $this->raw = $raw;
    $this->headerType = $header_type;
    $this->expected = $expected;
    $this->mustFail = $must_fail;
    $this->canFail = $can_fail;
    $this->canonical = $canonical;
  }

  /**
   * Get the test name.
   *
   * @return string
   *   The test name.
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * Get the raw lines.
   *
   * @return string[]
   *   The raw lines.
   */
  public function getRaw(): array {
    return $this->raw;
  }

  /**
   * Get the header type.
   *
   * @return string
   *   The type.
   */
  public function getHeaderType(): string {
    return $this->headerType;
  }

  /**
   * The expected value.
   *
   * @return mixed
   *   The value.
   */
  public function getExpected(): mixed {
    return $this->expected;
  }

  /**
   * Check the test must fail.
   *
   * @return bool
   *   The result.
   */
  public function mustFail(): bool {
    return $this->mustFail;
  }

  /**
   * Check the test can fail.
   *
   * @return bool
   *   The result.
   */
  public function canFail(): bool {
    return $this->canFail;
  }

  /**
   * Get the canonical values.
   *
   * @return array
   *   The values.
   */
  public function getCanonical(): array {
    return $this->canonical;
  }

  /**
   * Load the structured field test cases.
   *
   * @param string $filename
   *   The filename.
   *
   * @return \Xylemical\Http\StructuredFieldTestCase[]
   *   The test cases.
   */
  public static function load(string $filename): array {
    $tests = [];

    $contents = json_decode(file_get_contents($filename), TRUE);
    foreach ($contents as $test) {
      $tests[] = new StructuredFieldTestCase(
        $test['name'] ?? '',
        $test['raw'] ?? [],
        $test['header_type'] ?? 'item',
        $test['expected'] ?? NULL,
        $test['must_fail'] ?? FALSE,
        $test['can_fail'] ?? FALSE,
        $test['canonical'] ?? []
      );
    }

    return $tests;
  }

}
