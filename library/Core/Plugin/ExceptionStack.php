<?php

namespace Core\Plugin;

use SplStack, IteratorAggregate, Countable;

class ExceptionStack extends Exception implements IteratorAggregate, Countable
{   
    protected $stack;    
    
    function __construct()
    {
        $this->stack   = new SplStack;
        $this->message = "There were exceptions while loading the plugins.";
    }
    
    function push(\Exception $e)
    {
        $this->stack->push($e);
        return $this;
    }
    
    function pop()
    {
        return $this->stack->pop();
    }
    
    function count()
    {
        return sizeof($this->stack);
    }
    
    function getIterator()
    {
        return $this->stack;
    }
    
    function __toString()
    {
        $cnt = 1;
        $message = $this->message;
        
        foreach ($this->stack as $e) {
            $message .= sprintf("\n%d: %s", $cnt++, $e);
        }
        return $message;
    }
}
