<?php

class PluginRegistry implements IteratorAggregate
{
  
  protected $_plugins = array();
  
  public function add($id, Plugin $plugin)
  {
    if(array_key_exists($id, $this->_plugins)) {
      throw new Exception("Plugin already exists in the Registry. 
        Please make sure its name is unique.");
    }
    $this->_plugins[$id] = $plugin;
    
    return $this;
  }

  public function getIterator()
  {
    return new PluginRegistryIterator($this);
  }

  public function toArray()
  {
    return $this->_plugins;
  }
  
  public function __set($id, Plugin $plugin)
  {
    $this->add($id, $plugin);
  }
  
  public function __get($id)
  {
    if(array_key_exists($id, $this->_plugins)) {
      return $this->_plugins[$id];
    }
    
    throw new Exception("Plugin {$id} was not found. Please make sure you
      have correctly installed it");
  }

  
}
