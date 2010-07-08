<?php

class DependencyNotInstalledException extends Exception
{
  protected static $_failedDependencies = array();
  
  public function __construct($dependency = null)
  {
    if(!is_null($dependency)) {
      self::$_failedDependencies[] = $dependency;
    }
    
    $this->message = "The following Namespaces/Libraries are required by this "
      . "Application: " . join(self::$_failedDependencies, ", ");
    
    $this->code = 513;
  }
  
  public function hasFailedDependency($dependency)
  {
    return in_array($dependency, self::$_failedDependencies) ? true : false;
  }
  
  public function getFailedDependencies()
  {
    return self::$_failedDependencies;
  }
  
  public static function clearFailedDependencies()
  {
    $this->_failedDependencies = array();
  }
}
