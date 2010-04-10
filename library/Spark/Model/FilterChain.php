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
class Spark_Model_FilterChain implements Spark_Model_FilterChain_Interface
{
  
  /**
   * @var array
   */
  protected $_filters = array();
  
  /**
   * addFilter() - Add an filter instance to the chain
   *
   * @param Spark_Model_Filter_Interface $filter The Filter which should be chained
   * @return Spark_Model_FilterChain - Provide an fluent Interface
   */
  public function addFilter(Spark_Model_Filter_Interface $filter)
  {
    $this->_filters[] = $filter;
    
    return $this;
  }
  
  /**
   * processFilters() - Sequently execute every Filter in the chain
   *
   * @param Spark_Model_Entity $entity The Entity, which contents should be filtered
   */
  public function processFilters(Spark_Model_Entity $entity)
  {
    foreach($this->_filters as $filter) {
      $filter->execute($entity);
    }
  }
  
}
