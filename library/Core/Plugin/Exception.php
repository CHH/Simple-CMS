<?php

namespace Core\Plugin;

class Exception extends \Exception
{
    function setPrevious(\Exception $previous)
    {
        $this->previous = $previous;
        return $this;
    }
}
