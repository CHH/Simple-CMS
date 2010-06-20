<?php
/**
 * Spark Framework
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark_FilterChain
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */

/**
 * @category   Spark
 * @package    Spark_FilterChain
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
class Spark_FilterChain 
  implements Spark_FilterChainInterface, Spark_UnifiedConstructorInterface
{
  
  /**
   * @var string
   */
  private $_filterClass = "Spark_FilterInterface";
  
  /**
   * @var array
   */
  protected $_filters = array();

  public function __construct($options = null)
  {
    $this->setOptions($options);
  }

  public function setOptions($options)
  {
    Spark_Object_Options::setOptions($this, $options);
    return $this;
  }

  public function setFilters(array $filters)
  { 
    $this->_filters = array();
    
    foreach($filters as $filter) {
      $this->add($filter);
    }
    return $this;
  }
  
  public function accept($filterClass)
  {
    $this->setFilterClass($filterClass);
  }
  
  public function setFilterClass($filterClass)
  {
    $this->_filterClass = $filterClass;
  }
  
  /**
   * add() - Add an filter instance to the chain
   *
   * @param Spark_FilterInterface $filter The Filter which should be chained
   * @return Spark_FilterChain - Provide an fluent Interface
   */
  public function add($filter)
  {
    if($filter instanceof $this->_filterClass) {
      $this->_filters[] = $filter;
    } else {
      throw new Spark_Exception("Please supply a Filter inheriting from {$this->_filterClass} instead of "
        . gettype($filter) . ".");
    }
    
    return $this;
  }

  /**
   * process() - Sequently executes every Filter in the chain
   *
   * @param mixed $in
   * @param mixed $out
   */
  public function process($in, $out)
  {
    foreach($this->_filters as $filter) {
      $filter->execute($in, $out);
    }
  }

}
