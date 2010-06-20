<?php
/**
 * Base Class for Collections, which can be sorted and iterated over
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
class Spark_Model_Collection implements Iterator, ArrayAccess
{
  
  /**
   * @var array
   */
  protected $_data = array();
  
  /**
   * @var int
   */
  protected $_pos = 0;
  
  /**
   * @var $_sortField Used to pass the name of the entity property on which the 
   *                  sorting should be done
   */
  protected $_sortField = null;
  
  public function __construct(array $data = array()) 
  {
    $this->_data = $data;
  }
  
  public function prepend($data)
  {
    $this->_data = $this->_merge($data, $this->_data);
    return $this;
  }
  
  public function append($data) 
  {
    $this->_data = $this->_merge($this->_data, $data);
    return $this;
  }
  
  public function sortBy($field, $mode = "asc")
  {
    $mode = strtolower($mode);
    
    $this->_sortField = $field;
    
    switch($mode) {
      case "asc":
        uasort($this->_data, array($this, "_sortAsc"));
      break;
      
      case "desc":
        uasort($this->_data, array($this, "_sortDesc"));
      break;
    }
    
    unset($this->_sortField);
    
    return $this;
  }
  
  protected function _sortAsc($a, $b)
  {
    $field = $this->_sortField;
    
    $a = $a->$field;
    $b = $b->$field;
    
    if($a == $b) {
      return 0;
    }
    
    return ($a > $b) ? 1 : -1;
  }
  
  protected function _sortDesc($a, $b)
  {
    $field = $this->_sortField;
    
    $a = $a->$field;
    $b = $b->$field;
    
    if($a == $b) {
      return 0;
    }
    
    return ($a > $b) ? -1 : 1;
  }
  
  public function toArray()
  {
    return $this->_data;
  }
  
  public function offsetSet($offset, $value)
  {
    if($offset == null) {
      $this->_data[] = $value;
    } else {
      $this->_data[$offset] = $value;
    }
  }
  
  public function offsetGet($offset)
  {
    return $this->_data[$offset];
  }
  
  public function offsetExists($offset)
  {
    return isset($this->_data[$offset]);
  }
  
  public function offsetUnset($offset)
  {
    unset($this->_data[$offset]);
  }
  
  /**
   * current() - Returns the value of the current key as defined in Iterator Interface
   *
   * @return mixed
   */
  public function current()
  {
    return current($this->_data);
  }

  /**
   * rewind() - Resets the position in the array as defined in Iterator Interface
   */
  public function rewind()
  {
    $this->_pos = 0;
    reset($this->_data);
  }

  /**
   * valid() - Checks if the current position has a valid item
   * 
   * @return bool
   */
  public function valid()
  {
    return ($this->_pos < sizeof($this->_data));
  }

  /**
   * next() - Moves to the next position within the array
   */
  public function next()
  {
    $this->_pos++;
    next($this->_data);
  }

  /**
   * key() - Returns the key at the current position
   * @return mixed
   */
  public function key()
  {
    return key($this->_data);
  }
  
  protected function _merge($data1, $data2)
  {
    if($data1 instanceof Spark_Model_Collection) {
      $data1 = $data1->toArray();
    }
    
    if($data2 instanceof Spark_Model_Collection) {
      $data2 = $data2->toArray();
    }
    
    array_values($data1);
    array_values($data2);
    
    return array_merge($data1, $data2);
  }
  
}

