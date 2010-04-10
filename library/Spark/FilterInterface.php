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
interface Spark_FilterInterface
{

  /**
   * execute() - Gets called by the Filter chain
   *
   * @param object $in
   * @param object $out
   */
  public function execute($in, $out);
  
}
