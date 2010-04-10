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
 * @license http://www.opensource.org/licenses/mit-license.php
 */

/**
 * @category   Spark
 * @package    Spark
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license http://www.opensource.org/licenses/mit-license.php
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
