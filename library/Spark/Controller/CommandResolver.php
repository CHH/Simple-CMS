<?php

class Spark_Controller_CommandResolver 
  implements Spark_Controller_CommandResolverInterface, Spark_UnifiedConstructorInterface
{
  
  protected $_commandDirectory;
  
  protected $_defaultCommandName = "Default";
  
  protected $_commandSuffix = "Command";
  
  protected $_moduleDirectory;
  
  protected $_moduleCommandDirectory = "commands";
  
  public function __construct($options = null)
  {
    if(!is_null($options)) {
      $this->setOptions($options);
    }
  }
  
  public function setOptions($options)
  {
    Spark_Object_Options::setOptions($this, $options);
    return $this;
  }
  
  public function getCommand(Spark_Controller_RequestInterface $request)
  { 
    
    if(!is_null($request->getModuleName())) {
      $command = $this->_loadCommand($request->getCommandName(), $request->getModuleName());
      return $command;
      
    } elseif(!is_null($request->getCommandName())) {
      $command = $this->_loadCommand($request->getCommandName());
      return $command;
      
    } else {
      return $this->_loadCommand($this->getDefaultCommandName());
    }
  }
  
  public function getCommandByName($commandName, $moduleName = null)
  {
    return $this->_loadCommand($commandName, $moduleName);
  }
  
  protected function _loadCommand($commandName, $moduleName = null)
  {
    $className = ucfirst($commandName) . $this->getCommandSuffix();
    
    if($moduleName) {
      $path = $this->getModuleDirectory() . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR
              . $this->getModuleCommandDirectory() . DIRECTORY_SEPARATOR . $className . ".php";
      
      $className = ucfirst($moduleName) . "_" . $className;
      
    } else {
      $path = $this->getCommandDirectory() . DIRECTORY_SEPARATOR
              . $className . ".php";
    }
    
    if(!file_exists($path)) {
      return false;
    }
    
    include_once $path;
    
    if(!class_exists($className, false)) {
      return false;
    }
    
    $command = new $className;
    
    if(!($command instanceof Spark_Controller_CommandInterface)) {
      return false;
    }
    return $command;
  }
  
  public function getCommandDirectory()
  {
    return $this->_commandDirectory;
  }
  
  public function setCommandDirectory($commandDirectory)
  {
    $this->_commandDirectory = $commandDirectory;
    return $this;
  }
  
  public function getCommandSuffix()
  {
    return $this->_commandSuffix;
  }
  
  public function setCommandSuffix($suffix)
  {
    $this->_commandSuffix = $suffix;
    return $this;
  }
  
  public function getDefaultCommandName()
  {
    return $this->_defaultCommandName;
  }
  
  public function setDefaultCommandName($commandName)
  {
    $this->_defaultCommandName = $commandName;
    return $this;
  }
  
  public function getModuleDirectory()
  {
    return $this->_moduleDirectory;
  }
  
  public function setModuleDirectory($directory)
  {
    $this->_moduleDirectory = $directory;
    return $this;
  }
  
  public function getModuleCommandDirectory()
  {
    return $this->_moduleCommandDirectory;
  }
  
  public function setModuleCommandDirectory($directory)
  {
    $this->_moduleCommandDirectory = $directory;
    return $this;
  }
  
}