<?php

class Depender
{ 

  const DS = DIRECTORY_SEPARATOR;  
  
  protected $_dependencies;
  
  protected $_loadPath;
  
  public function __construct($dependencies = array())
  {
    $this->setDependencies($dependencies);
  }
  
  public function checkAll(array $dependencies = array())
  {
    if (!$dependencies) {
      $dependencies = $this->_dependencies;
    }
    
    $failed = false;
    
    foreach ($dependencies as $dependency) {
      try {
        $this->check($dependency);
      } catch(DependencyNotInstalledException $e) {
        $failed = true;
      }
    }
    
    if($failed) {
      throw new DependencyNotInstalledException;
    }
    
    return $this;
  }
  
  public function check($dependency)
  {
    if (is_string($this->_loadPath)) {
      if (is_dir($this->_loadPath . self::DS . $dependency)) {
        return $this;
      }
    } elseif (is_array($this->_loadPath)) {
      /**
       * Check all paths for the dependency
       */
      foreach ($this->_loadPath as $path) {
        $nsPath = $path . self::DS . $dependency;
        if (is_dir($nsPath)) {
          return $this;
        }
      }
    }
    throw new DependencyNotInstalledException($dependency);
  }
  
  public function setLoadPath($loadPath)
  {
    if (strpos($loadPath, PATH_SEPARATOR)) {
      $loadPath = explode(PATH_SEPARATOR, $loadPath);
    }
    $this->_loadPath = $loadPath;
    return $this;
  }
  
  public function getLoadPath($loadPath)
  {
    return $this->_loadPath;
  }
  
  public function setDependencies(array $dependencies)
  {
    $this->_dependencies = $dependencies;
    return $this;
  }
  
}
