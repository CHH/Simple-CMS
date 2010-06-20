<?php
/**
 * Spark Framework
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */

/**
 * @category   Spark
 * @package    Spark
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
interface Spark_FilterChainInterface
{
  /**
   * add() - Adds a filter instance to the filter chain
   *
   * @param object $filter
   * @return self
   */
  public function add($filter);

  /**
   * process() - Calls every filter in the chain with the given parameters
   *
   * @param object $in
   * @param object $out
   */
  public function process($in, $out);

}
