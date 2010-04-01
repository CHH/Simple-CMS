<?php

class PluginRegistryIterator implements Iterator
{
  protected $_pos = 0;

  protected $_plugins = array();
  
  public function __construct(PluginRegistry $registry)
  {
    $this->_plugins = $registry->toArray();
  }
  
  public function current()
  {
    return current($this->_plugins);
  }

  public function rewind()
  {
    $this->_pos = 0;
    reset($this->_plugins);
  }

  public function valid()
  {
    return ($this->_pos < sizeof($this->_plugins));
  }

  public function next()
  {
    $this->_pos++;
    next($this->_plugins);
  }

  public function key()
  {
    return key($this->_plugins);
  }
}
