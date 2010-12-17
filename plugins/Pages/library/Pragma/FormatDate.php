<?php
/**
 * A simple pragma which formats the date of the variable
 * 
 * Usage:
 * {{%FORMAT-DATE format=short}}
 * {{some_var_with_datetime}}
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
namespace Plugin\Pages\Pragma;

use DateTime,
    Phly\Mustache\Pragma\AbstractPragma,
    Phly\Mustache\Lexer;

class FormatDate extends AbstractPragma
{
    const FORMAT_SHORT = "short";    
    const FORMAT_LONG  = "long";
    
    protected $name = "FORMAT-DATE";
    
    protected $tokensHandled = array(
        Lexer::TOKEN_VARIABLE,
        Lexer::TOKEN_VARIABLE_RAW
    );
    
    function handle($token, $data, $view, Array $options)
    {
        $escape = true;
        
        // Should we escape?
        switch ($token) {
            case Lexer::TOKEN_VARIABLE:
                break;
            case Lexer::TOKEN_VARIABLE_RAW:
                $escape = false;
                break;
            default:
                return;
        }
        
        $format = array_delete_key("format", $options) ?: "Y-m-d H:i:s";
        
        // Handle some predefined formats
        switch ($format) {
            case self::FORMAT_SHORT:
                $format = "j. M. y G:i";
                break;
            case self::FORMAT_LONG:
                $format = "j. F Y G:i";
                break;
            default:
                // do nothing
        }
        
        $date = new DateTime($view[$data]);
        $formatted = $date->format($format);
        
        return $escape ? $this->getRenderer()->escape($formatted) : $formatted;
    }
}
