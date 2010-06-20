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
class Spark_Object_Options 
{
  
  /**
   * Calls the Setter Methods in the given object context for every key
   * in the supplied options. The Name of the Setter Method must be camelCased
   * and the key in the $options Array must have underscores  
   * e.g. for the key "file_name" the Setter's name is "setFileName".
   *
   * @param object $context The object context in which the Setters get called
   * @param mixed  $options Can either be an Array containing key => value pairs
   *                        or an instance of Zend_Config
   * @param array  $settableOptions Optional list of fields which are settable 
   *                                on the object
   */
  static public function setOptions($context, $options, array $settableOptions = null)
  {
    if(!is_object($context) or is_null($options)) {
      return;
    }
    
    if($options instanceof Zend_Config) {
      $options = $options->toArray();
    }
    
    if(is_array($options)) {
      foreach($options as $key => $value) {
        if(!is_null($settableOptions) and !array_key_exists($key, $settableOptions)) {
          continue;
        }
        
        $setterName = self::_getSetterName($key);
        $context->$setterName($value);
      }
    }
  }
  
  /**
   * Converts underscore_option_name to camelCasedSetterName
   *
   * @param  string $option The Name of the Option which should be converted
   * @return string         The Name of the Setter Method
   */
  static protected function _getSetterName($option) {
    return "set" . str_replace(" ", "", ucwords(str_replace("_", " ", strtolower($option))));
  }
  
}
