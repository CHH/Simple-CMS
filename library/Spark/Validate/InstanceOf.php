<?php
/**
 * Spark Framework
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark_Validate
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */

/**
 * @category   Spark
 * @package    Spark_Validate
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
class Spark_Validate_InstanceOf extends Zend_Validate_Abstract
{
  
  const NOT_INSTANCE = "notInstanceOf";

  /**
   * @var array
   */
  protected $_messageTemplates = array(
    self::NOT_INSTANCE => "'%value%' is not an Instance of '%class%'"
  );

  /**
   * @var array
   */
  protected $_messageVariables = array(
    'class' => '_class'
  );

  /**
   * @var string
   */
  protected $_class;

  /**
   * __construct() - Sets the classname for the comparison
   *
   * @param string $class
   */
  public function __construct($class)
  {
    $this->setClass($class);
  }

  /**
   * getClass()
   *
   * @return string
   */
  public function getClass()
  {
    return $this->_class;
  }

  /**
   * setClass()
   *
   * @param string $class
   */
  public function setClass($class)
  {
    $this->_class = $class;
    return $this;
  }

  /**
   * isValid() - Checks if the object supplied as value is an instance of
   *  the classname which got set by the Constructor or by the setClass() method
   *
   * @param object $value
   * @return bool
   */
  public function isValid($value)
  {
    $this->_setValue($value);
    
    if(!($value instanceof $this->_class)) {
      $this->_error(self::NOT_INSTANCE);
      return false;
    }
    return true;
  }
  
}
