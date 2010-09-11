<?php

class PageNew
{
    protected $name;
    protected $path = DIRECTORY_SEPARATOR;
    protected $content;
    protected $created;
    protected $modified;
    protected $attributes = array();
    
    public function __construct($page = array())
    {
        /*
         * Treat first argument as filename if string is given
         */ 
        if (is_array($page)) {
            Spark_Options::setOptions($this, $page);
        }
    }
    
    public static function loadFile($file)
    {
        $renderer = self::getRenderer();
        
        if (strpos($file, $this->extension) === false) {
            $file .= $this->extension;
        }
        
        $page = new self;
        
        // TODO: Set Name, path, created, modified of page
        $page->setContent($renderer->render($file));

        return $page;
    }
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
    
    public function getContent($content)
    {
        return $this->content;
    }
    
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }
    
    public function getCreated()
    {
        return $this->created;
    }
    
    public function setModified($modified)
    {
        $this->modified = $modified;
        return $this;
    }
    
    public function getModified()
    {
        return $this->modified;
    }
    
    public function setAttribute($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
        return $this;
    }
    
    public function getAttribute($attribute)
    {
        if (!isset($this->attributes[$attribute])) {
            return null;
        }
        return $this->attributes[$attribute];
    }
}
