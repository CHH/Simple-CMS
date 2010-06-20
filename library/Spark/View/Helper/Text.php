<?php

class Spark_View_Helper_Text extends Zend_View_Helper_Abstract
{

  public function text()
  {
    return $this;
  }
  
  /**
   * excerpt() - Takes a string and gives back the shortened String.
   * Looks for the next space within the maximum length plus/minus the tolerance and
   * gives back the string up to this position.
   *
   * @param string  $string    String to shorten
   * @param int     $maxLength Maximum length of the shortened string
   *                           (in characters, approximately)
   * @param string  $ellipsis  Takes the ellipsis which should be appended to the
   *                           shortened string (default is the typographic ellipsis)
   * @param int     $tol       The space is looked up in the range of $maxLength +/-
   *                           $tol
   * @return string            Shortened string
   */
  public function excerpt($string, $maxLength = 100, $ellipsis = "&hellip", $tol = 10)
  {
    
    if( strlen($string) > $maxLength ) {
      if( ($pos = strpos($string, " ",$offset = $maxLength)) && ($pos <= ($maxLength + $tol)) ) {
        
      } elseif( $pos = strpos($string," ",$offset = $maxLength - $tol) ) {
        
      } else {
        $pos = $maxLength;
      }
      
      $string = substr($string,0,$pos);
      return $string.$ellipsis;
      
    } else {
      return $string;
    }
  }

}

?>
