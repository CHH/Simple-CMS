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
class Spark_Model_Entity 
  implements Spark_Model_SavableInterface, Spark_Model_ValidateableInterface
{
  /**
   * @var array
   */
  protected $_data       = array();
  
  /**
   * @var array
   */
  protected $_references = array();
  
  /**
   * @var array
   */
  protected $_validators = array();
  
  /**
   * @var bool
   */
  protected $_isValid  = true;
  
  /** 
   * @var array
   */
  protected $_invalidFields = array();
  
  protected $_dataBinder = null;
  
  /**
   * __construct() - Calls the init Method and the keys in $_data property are 
   *                 loaded with the given $data Array              
   *
   * @param  array $data              Data supplied to the load() Method
   * @param  bool  $ignoreNotExisting Not Existing keys are ignored, see load()
   * @return Spark_Model_Entity
   */
  public function __construct($data = null, $ignoreNotExisting = false)
  {
    $this->init();
    $this->load($data, $ignoreNotExisting);
  }
  
  /**
   * init() - Gets called by the constructor. Used to set up the entity (e.g. 
   *          for Validation) without overriding the constructor. Should be used
   *          to set up an inherited class
   */
  public function init()
  {}
  
  /**
   * toArray() - Returns the value of the $_data property
   *
   * @return array
   */
  public function toArray($serializeReferences = false)
  {
    $data = $this->_data;
    
    $dataBinder = $this->getDataBinder();
    
    foreach($this->_data as $key => $value) {
      if($dataBinder->hasBinding($key)) {
        $data[$key] = $dataBinder
                        ->getBinding($key)
                        ->setReferenceId($this->getReferenceId($key))
                        ->getReference();
        if($serializeReferences) {
          $data[$key] = $data[$key]->toArray();
        }
      }
    }
    
    return $data;
  }
  
  public function asSavable()
  {
    return $this->_data;
  }
  
  /**
   * load() - Loads the supplied data into the entity
   * @deprecated Use setData() instead
   * @param array $data the data which should be loaded
   * @param bool  $ignoreNotExisting If this is true and a key exists in 
   *                                 the supplied array but not in the $_data
   *                                 property, the key is not set and no error is raised
   * @return Spark_Model_Entity Provides an fluent interface
   */
  public function load($data, $ignoreNotExisting = false) 
  {
    return $this->setData($data, $ignoreNotExisting);
  }
  
  /**
   * setData() - Loads the supplied data into the entity
   * @param array $data the data which should be loaded
   * @param bool  $ignoreNotExisting If this is true and a key exists in 
   *                                 the supplied array but not in the $_data
   *                                 property, the key is not set and no error is raised
   * @return Spark_Model_Entity Provides an fluent interface
   */
  public function setData($data, $ignoreNotExisting = false)
  {
    if(!is_null($data)) {
      foreach($data as $property => $value) {
        if(!array_key_exists($property, $this->_data) and true === $ignoreNotExisting) {
          continue;
        }
        
        $this->$property = $value;
      }
    }
    return $this;
  }
  
  public function getDataBinder()
  {
    if(is_null($this->_dataBinder)) {
      $this->_dataBinder = new Spark_Model_DataBinder;
    }
    return $this->_dataBinder;
  }
  
  /**
   * setReferenceId() - Stores the id for an referenced entity
   *
   * @param string $name The name of the referenced entity
   * @param mixed  $id   Unique Value under which the referenced entity can be 
   *                     retrieved from the corresponding Data Mapper
   * @return Spark_Model_Entity Provides an fluent interface
   */
  public function setReferenceId($name, $id)
  {
    $this->_references[$name] = $id;
    return $this;
  }
  
  /**
   * getReferenceId() - Retrieves the id of an referenced entity.
   *
   * @param string $name The Name under which the id is stored
   * @return mixed The Id of the referenced entity
   */
  public function getReferenceId($name)
  {
    if(array_key_exists($name, $this->_references)) {
      return $this->_references[$name];
    }
  }
  
  /**
   * __set() - Interceptor method, maps the keys in the $_data property to
   *           Object Properties, If a validator is registered for the key, the 
   *           value is passed to the validator. Stores the invalid Fields with
   *           the validation messages, sets the $_isValid $property.
   * 
   * @param string $property The name of the key
   * @param mixed  $value    The value which should be stored
   */
  public function __set($property, $value)
  {
    if(!array_key_exists($property, $this->_data)) {
      throw new Spark_Model_Exception("You cannot add the Property \"{$property}\" to this object!");
    }
    
    if(array_key_exists($property, $this->_validators)) {
      $validator = $this->_validators[$property];
      
      if(!($validator instanceof Zend_Validate_Interface)) {
        throw new Zend_Validate_Exception("The Validator must implement the"
         . " Zend_Validate_Interface"); 
      }
      
      if($validator->isValid($value)) {
        $this->_data[$property] = $value;
        
      } else {
        $this->_isValid = false;
        $this->_invalidFields[$property] = $validator->getMessages();
      }
      
    } else {
      $this->_data[$property] = $value;
    }
  }
  
  /**
   * __get() - Interceptor Method, returns the key from the $_data Property
   *
   * @param  string $property The key whos value should be retrieved
   * @return mixed
   */
  public function __get($property) {
    if($this->getDataBinder()->hasBinding($property)) {
      return $this->getDataBinder()
                   ->getBinding($property)
                   ->setReferenceId($this->getReferenceId($property))
                   ->getReference();
    }
    
    if(!array_key_exists($property, $this->_data)) {
      throw new Spark_Model_Exception("The Property \"{$property}\" " 
                                       . "was not found!");
    }
    return $this->_data[$property];
  }
  
  /**
   * isValid() - Checks if the Entity has invalid keys, is set by the 
   *             validation mechanism
   *
   * @return bool
   */
  public function isValid()
  {
    return $this->_isValid ? true : false;
  }
  
  /**
   * getInvalid() - If the entity has invalid keys, the keys get returned with
   *                their validation messages, otherwise NULL is returned
   *
   * @return array
   */
  public function getInvalid()
  {
    return $this->_invalidFields ? $this->_invalidFields : null;
  }
  
  /**
   * __isset()
   *
   * @return bool
   */
  public function __isset($name)
  {
    return isset($this->_data[$name]);
  }     
  
  /**
   * __unset()
   *
   * @return bool
   */
  public function __unset($name)    
  {
    if (isset($this->_data[$name])) {
      unset($this->_data[$name]);
    }    
  }
  
}

?>
