<?php
/**
 * Spark Framework
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark_Model
 * @copyright  Copyright (c) 2009 Christoph Hochstrasser
 * @license http://www.opensource.org/licenses/mit-license.php
 */

/**
 * @category   Spark
 * @package    Spark_Model
 * @copyright  Copyright (c) 2009 Christoph Hochstrasser
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class Spark_Model_Validate_Exception extends Zend_Exception
{
  /**
   * @var string
   */
  protected $_field = "";
  
  /**
   * @var array
   */
  protected $_messages = array();
  
  /**
   * __construct() - Takes the field and the corresponding messages
   * @param  string $field              The name of the field
   * @param  array  $validationMessages Array of validation messages for the field
   * @return Spark_Model_Validate_Exception
   */
  public function __construct($field, array $validationMessages)
  {
    $this->_messages = $validationMessages;
    $this->_field = $field;
  }
  
  /**
   * getField() - Returns the $_field property
   * @return string
   */
  public function getField()
  {
    return $this->_field;
  }
  
  /**
   * getMessages - Return the Message Array
   * @return array
   */
  public function getMessages()
  {
    return $this->_messages;
  }
}
