<?php
/**
 * Manages Bindings in the Entity
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark_Model
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */

/**
 * @category   Spark
 * @package    Spark_Model
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
class Spark_Model_DataBinder
{
  
  protected $_bindings = array();
  protected $_dataBinder = null;
  
  public function addBinding(Spark_Model_DataBinding $binding)
  {
    $this->_bindings[$binding->getIdentifier()] = $binding;
    return $this;
  }
  
  public function hasBinding($identifier)
  {
    return array_key_exists($identifier, $this->_bindings) ? true : false;
  }
  
  public function getBinding($identifier)
  {
    if($this->hasBinding($identifier)) {
      return $this->_bindings[$identifier];
    }
  }
  
}
