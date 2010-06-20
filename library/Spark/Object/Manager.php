<?php
/**
 * Spark Framework
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark_Object
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */

/**
 * @category   Spark
 * @package    Spark_Object
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
class Spark_Object_Manager
{
  /**
   * @var array
   */
  static protected $_bindings = array();

  /**
   * @var array
   */
  static protected $_singletons = array();

  /**
   * @var array
   */
  static protected $_config;
  
  /**
   * create() - creates a new Instance of the class on every call
   *
   * @param string $className
   * @return object
   */
  static public function create()
  {
    $args = func_get_args();
    $className = $args[0];
    unset($args[0]);
    
    return self::_instantiateClass($className, $args);
  }
  
  /**
   * get() - Returns a shared Instance of a Class, 
   *  instantiates a new Object only on first call
   *
   * @param string $className
   * @return object
   */
  static public function get()
  {
    $args = func_get_args();
    $className = $args[0];
    unset($args[0]);
    
    if(!isset(self::$_singletons[$className])) {
      self::$_singletons[$className] = self::_instantiateClass($className, $args);
    }
    
    return self::$_singletons[$className];
  }

  /**
   * _instantiateClass() - Does the heavy lifting in object instantiation
   *
   * @param string $className
   * @param array  $args Numerical indexed array, which contains the arguments
   *                     for the object constructor
   * @return object
   */
  static protected function _instantiateClass($className, $args)
  {
    if(!$className) {
      throw new BadMethodCallException("The first argument must contain the classname, NULL given");
    }
    
    $class = new ReflectionClass($className);

    if($class->implementsInterface("Spark_UnifiedConstructorInterface")) {
      if(isset($args[1]) and (is_array($args[1]) or $args[1] instanceof Zend_Config)) {
        $options = $args[1];
      } elseif(self::hasBinding($className)) {
        $options = self::getBinding($className)->getOptions();
      } elseif(isset(self::$_config[$className])) {
        $options = self::$_config[$className];
      } else {
        $options = null;
      }

      return $class->newInstance($options);
    }
    
    if(sizeof($args) > 0) {
      return $class->newInstanceArgs($args);
    }
    
    return $class->newInstance();
  }

  /**
   * getConfig() - Returns the static config array
   *
   * @return array
   */
  static public function getConfig()
  {
    return self::$_config;
  }
  
  /**
   * setConfig() - Sets a config array/object.
   * On Instantiation the classname is looked up in this config and the value
   * for the classname is used as options for the class
   * 
   * @param mixed $config
   */
  static public function setConfig($config)
  {
    if($config instanceof Zend_Config) {
      self::$_config = $config->toArray();
    } elseif(is_array($config)) {
      self::$_config = $config;
    } else {
      throw new InvalidArgumentException("Config must be an Instance of 
       Zend_Config or an Array");
    }
  }

  /**
   * addBinding() - Binds a set of options to a class through an OptionBinding Object
   *
   * @param Spark_Object_OptionBinding $binding
   */
  static public function addBinding(Spark_Object_OptionBinding $binding)
  {
    self::$_bindings[$binding->getClass()] = $binding;
  }

  /**
   * getBinding() - Returns the Option Binding for the given Class
   *
   * @param string $class
   * @return Spark_Object_OptionBinding
   */
  static public function getBinding($class)
  {
    if(self::hasBinding($class)) {
      return self::$_bindings[$class];
    }
    return null;
  }

  /**
   * hasBinding() - Checks if a Binding for the given Class exists
   *
   * @param string class
   * @return bool
   */
  static public function hasBinding($class)
  {
    return array_key_exists($class, self::$_bindings) ? true : false;
  }
}
