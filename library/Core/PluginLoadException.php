<?php

class PluginLoadException extends PluginException
{
  
  protected $_failedDependencies = array();
  
  protected $_failedPlugins = array();
  
  public function setFailedPlugins(array $plugins)
  {
    $this->_failedPlugins = $plugins;
    return $this;
  }
  
  public function getFailedPlugins()
  { 
    return $this->_failedPlugins;
  }
  
  public function setFailedDependencies(array $dependencies)
  {
    $this->_failedDependencies = $dependencies;
    return $this;
  }
  
  public function getFailedDependencies()
  {
    return $this->_failedDependencies;
  }
  
}