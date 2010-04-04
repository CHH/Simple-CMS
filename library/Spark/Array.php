<?php
/**
 * Spark Framework
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark_Array
 * @copyright  Copyright (c) 2009 Christoph Hochstrasser
 * @license http://www.opensource.org/licenses/mit-license.php
 */

/**
 * @category   Spark
 * @package    Spark_Array
 * @copyright  Copyright (c) 2009 Christoph Hochstrasser
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class Spark_Array
{
  
  /**
   * @var array
   */
  protected $_data = array();
  
  /**
   * @param  array $data The data which should be loaded into the $_data property
   * @return Spark_Array
   */
  public function __construct($data)
  {
    $this->load($data);
  }
  
  /**
   * load() - Checks the type of the given data and sets the $_data property
   *
   * @param  array $data
   * @return Spark_Array
   */
  public function load($data)
  {
    if(!is_array($data) or $data instanceof ArrayObject) {
      throw new InvalidArgumentException("Data must be either an Array or
      an ArrayObject");
    }
    $this->_data = $data;
    return $this;
  }
  
  /**
   * indexByFirstLetter() - Index an array by the first letter of a given key
   * @param array $array The array which should be processed
   * @param mixed $key   The key which contains the string for the index
   * @return array       Array index by the first letter of the value of the key
   */
  public function indexByFirstLetterOfKey($key)
  {
    if( !is_array($this->_data) AND !($this->_data instanceof ArrayObject) ) {
      throw new InvalidArgumentException("You must supply an array or an object 
        that implements ArrayAccess");
    }
    
    $newArray = array();
    
    foreach( $this->_data as $value ) {
      if( !is_string($value[$key]) ) {
        throw new UnexpectedValueException("The supplied key does not contain a string");
      }
      
      $newArray[ strtoupper($value[$key][0]) ][] = $value;
    }
    
    return $newArray;
  }
  
  /**
   * indexByDate() - Formats the value of the given key as date 
   *                 and indexes the array approbiatly
   * @param string $dateIndex The key for the used value
   * return array
   */
  public function indexByDate($dateIndex='created')
  {
    foreach($this->_data as $item) {
      $newArray[date("Y-m-d",strtotime($item[$dateIndex]))][] = $item;
    }
    
    return $newArray;
  }
  
}

?>
