<?php

class BundleException extends Exception
{
  
  protected static $_failedBundles = array();
  
  public function __construct($failedBundle, $message = null)
  {
    self::$_failedBundles[] = $failedBundles;
    $this->message;
  }
  
  public function getFailedBundles()
  {
    return self::$_failedBundles;
  }
  
  public static function clearFailedBundles()
  {
    self::$_failedBundles = array();
  }
}
