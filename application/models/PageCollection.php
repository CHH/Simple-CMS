<?php

class PageCollection implements Iterator, ArrayAccess
{
  
  protected $_pages = array();
  
  /**
   * @var int
   */
  protected $_pos = 0;
  
  protected $_sortField = null;
  
  public function __construct($pages = array()) {
    $this->_pages = $pages;
  }
  
  public function sort($field, $mode = "asc")
  {
    $mode = strtolower($mode);
    
    $this->_sortField = $field;
    
    switch($mode) {
      case "asc":
        uasort($this->_pages, array($this, "_sortAsc"));
      break;
      
      case "desc":
        uasort($this->_pages, array($this, "_sortDesc"));
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
  
  public function offsetSet($offset, $value)
  {
    if($value instanceof Page) {
      if($offset == null) {
        $this->_pages[] = $value;
      } else {
        $this->_pages[$offset] = $value;
      }
    }
  }
  
  public function offsetGet($offset)
  {
    return $this->_pages[$offset];
  }
  
  public function offsetExists($offset)
  {
    return isset($this->_pages[$offset]);
  }
  
  public function offsetUnset($offset)
  {
    unset($this->_pages[$offset]);
  }
  
  /**
   * current() - Returns the value of the current key as defined in Iterator Interface
   *
   * @return mixed
   */
  public function current()
  {
    return current($this->_pages);
  }

  /**
   * rewind() - Resets the position in the array as defined in Iterator Interface
   */
  public function rewind()
  {
    $this->_pos = 0;
    reset($this->_pages);
  }

  /**
   * valid() - Checks if the current position has a valid item
   * 
   * @return bool
   */
  public function valid()
  {
    return ($this->_pos < sizeof($this->_pages));
  }

  /**
   * next() - Moves to the next position within the array
   */
  public function next()
  {
    $this->_pos++;
    next($this->_pages);
  }

  /**
   * key() - Returns the key at the current position
   * @return mixed
   */
  public function key()
  {
    return key($this->_pages);
  }
}
