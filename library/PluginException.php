<?php

class PluginException extends Exception
{
  
  protected $_pluginName;
  protected $_failedPlugins = array();

  
  public function __construct($name, $message = null, $code = 0, array $failedPlugins = array(), Exception $previous = null)
  {
    $this->_pluginName = $name;
    $this->message = $message;
    $this->code = $code;
    $this->_failedPlugins = $failedPlugins;
    $this->previous = $previous;
  }
  
  final public function getPluginName()
  {
    return $this->_pluginName;
  }
  
  public function setFailedPlugins(array $plugins)
  {
    $this->_failedPlugins = $plugins;
    return $this;
  }

  public function getFailedPlugins()
  {
    return $this->_failedPlugins;
  }
  
}