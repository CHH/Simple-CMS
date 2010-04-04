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
class Spark_Model_Filter_DateTime implements Spark_Model_Filter_Interface
{
  /**
   * @var string
   */
  protected $_property = "";
  
  /**
   * @var string
   */
  protected $_format = "";
  
  /**
   * __construct()
   *
   * @param string $property The property of the entity which should be modified
   * @param string $format   The Date/Time format for the date() function
   * @return Spark_Model_Filter_DateTime
   */
  public function __construct($property = "created", $format = "Y-m-d H:i:s")
  {
    $this->_property = $property;
    $this->_format = $format;
  }
  
  /**
   * execute() - Gets called by the FilterChain, modifies the given property of the entity 
   *             to contain a Date/Time
   * 
   * @param Spark_Model_Entity $entity The Entity which should be modified
   */
  public function execute(Spark_Model_Entity $entity)
  {
    $property = $this->_property;
    
    $entity->$property = date($this->_format); 
  }
  
}
