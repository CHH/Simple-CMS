<?php

class PluginRegistry implements IteratorAggregate
{
  
  /**
   * @var array
   */
  protected $_plugins = array();
  
  /**
   * add() - Add a plugin to the registry
   *
   * @param string $id Unique identifier for the plugin, usually taken from the name of
   *                   directory the plugin lives in
   * @param Plugin $plugin instance of the Plugin
   * @return PluginRegistry
   */
  public function add($id, Plugin $plugin)
  {
    if(array_key_exists($id, $this->_plugins)) {
      throw new Exception("Plugin already exists in the Registry. 
        Please make sure its name is unique.");
    }
    $this->_plugins[$id] = $plugin;
    
    return $this;
  }
  
  /**
   * has() - Checks if a given plugin has been loaded
   *
   * @param string $id Unique identifier of the plugin you want to check
   * @return bool
   */
  public function has($id)
  {
    return array_key_exists($id, $this->_plugins);
  }
  
  /**
   * get() - Returns the plugin instance for a given plugin id
   *
   * @param string $id
   * @return Plugin
   */
  public function get($id)
  {
    if($this->has($id)) {
      return $this->_plugins[$id];
    }

    throw new Exception("Plugin {$id} was not found. Please make sure you
      have correctly installed it");
  
  }
  
  /**
   * getIterator() - Returns the Iterator for the PluginRegistry as defined in 
   *                 the IteratorAggregate Interface
   *
   * @return PluginRegistryIterator
   */
  public function getIterator()
  {
    return new PluginRegistryIterator($this);
  }
  
  /**
   * toArray() - Returns the plugin instances in an associative array
   *             with the id's as keys
   *
   * @return array
   */
  public function toArray()
  {
    return $this->_plugins;
  }
  
  /**
   * __set() - Alias for add(), enables $pluginRegistry->$id = $plugin assignments
   * @see add()
   */
  public function __set($id, Plugin $plugin)
  {
    $this->add($id, $plugin);
  }
  
  /**
   * __get() - Alias for get(), enables accessing plugins directly throught the instance
   * @see get()
   */
  public function __get($id)
  {
    return $this->get($id);
  }

  
}
