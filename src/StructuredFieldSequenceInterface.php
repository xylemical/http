<?php

namespace Xylemical\Http;

/**
 * Provides structured fields allowed in sequences or dictionaries.
 */
interface StructuredFieldSequenceInterface extends StructuredFieldInterface {

  /**
   * Get the parameters for the structured field item.
   *
   * @return \Xylemical\Http\Parameters
   *   The parameters.
   */
  public function getParameters(): Parameters;

}
