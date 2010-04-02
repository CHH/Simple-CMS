<?php

class PluginRegistryIterator implements Iterator
{
  /**
   * @var int
   */
  protected $_pos = 0;
  
  /**
   * @var array
   */
  protected $_plugins = array();
  
  /**
   * __construct()
   *
   * @param PluginRegistry The plugin registry which should be iterated over
   */
  public function __construct(PluginRegistry $registry)
  {
    $this->_plugins = $registry->toArray();
  }
  
  /**
   * current() - Returns the value of the current key as defined in Iterator Interface
   *
   * @return mixed
   */
  public function current()
  {
    return current($this->_plugins);
  }
  
  /**
   * rewind() - Resets the position in the array as defined in Iterator Interface
   */
  public function rewind()
  {
    $this->_pos = 0;
    reset($this->_plugins);
  }
  
  /**
   * valid() - Checks if the current position has a valid item
   * 
   * @return bool
   */
  public function valid()
  {
    return ($this->_pos < sizeof($this->_plugins));
  }
  
  /**
   * next() - Moves to the next position within the array
   */
  public function next()
  {
    $this->_pos++;
    next($this->_plugins);
  }
  
  /**
   * key() - Returns the key (plugin id) at the current position
   * @return mixed
   */
  public function key()
  {
    return key($this->_plugins);
  }
}
