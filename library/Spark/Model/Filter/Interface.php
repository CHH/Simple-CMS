<?php
/**
 * Spark Framework
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark_Model
 * @copyright  Copyright (c) 2009 Christoph Hochstrasser
 * @license http://www.opensource.org/licenses/mit-license.php
 */

/**
 * @category   Spark
 * @package    Spark_Model
 * @copyright  Copyright (c) 2009 Christoph Hochstrasser
 * @license http://www.opensource.org/licenses/mit-license.php
 */
interface Spark_Model_Filter_Interface
{
  /**
   * execute() - Method which gets called by the Filter Chain, by Command Pattern
   * @param Spark_Model_Entity $entity The Entity which should be modified
   */
  public function execute(Spark_Model_Entity $entity);
  
}
