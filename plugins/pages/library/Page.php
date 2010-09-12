<?php

class Page
{
    protected static $searchPath;    
    protected static $defaultPageName = "index";
    
    const EXTENSION = ".txt";
    
    protected $name;
    protected $content;
    protected $created;
    protected $modified;
    protected $attributes = array();
    
    protected $absolutePath;
    
    public function __construct($page = array())
    {
        if (is_array($page)) {
            Spark_Options::setOptions($this, $page);
        }
    }
    
    public static function find($file)
    {
        $page = new self;
        return $page->loadFile($file);
    }
    
    public static function findAll($path) 
    {
        if (!is_string($path) or empty($path)) {
            throw new InvalidArgumentException("No path given");
        }
        
        foreach (self::getSearchPath() as $searchPath) {
            $absolutePath = $searchPath . DIRECTORY_SEPARATOR . $path;
            if (is_dir($absolutePath)) {
                break;
            }
        }
        
        if (!isset($absolutePath)) {
            throw new InvalidArgumentException("Path not found");
        }
        
        $directory = new DirectoryIterator($absolutePath);
        $pages     = array(); 
        
        foreach ($directory as $entry) {
            if (!$entry->isFile() or $entry->isDot() 
                or strpos(self::EXTENSION, pathinfo($entry->getFilename(), PATHINFO_EXTENSION) === false)) {
                continue;
            }
            $page = new self;
            $pages[] = $page->loadFile($path . DIRECTORY_SEPARATOR . $entry->getFilename());
        }
    }
    
    public function loadFile($file)
    {
        if (!$absolutePath = self::getFilename($file)) {
            return false;
        }
        
        $pathinfo = pathinfo($file);
        
        $this->setName($pathinfo["filename"]);
        $this->setModified(filemtime($absolutePath));
        $this->setPath($pathinfo["dirname"]);
        
        $this->absolutePath = $absolutePath;
        
        return $this;
    }

    protected static function getFilename($page)
    {
        $pathinfo = pathinfo($page);
        
        foreach (self::getSearchPath() as $searchPath) {
            $absoluteFilePath = $searchPath . DIRECTORY_SEPARATOR . $pathinfo["dirname"] 
                . DIRECTORY_SEPARATOR . $pathinfo["filename"];
            
            if (is_dir($absoluteFilePath)) {
                $absoluteFilePath .= DIRECTORY_SEPARATOR . self::$defaultPageName;
            }
            
            $absoluteFilePath .= self::EXTENSION;
            
            if (file_exists($absoluteFilePath)) {
                return $absoluteFilePath;
            }
        }
        
        return false;
    }
    
    public static function setSearchPath($path)
    {
        if (is_string($path) and !is_empty($path)) {
            self::$searchPath = array($path);
        } else if (is_array($path)) {
            self::$searchPath = $path;
        } else {
            throw new InvalidArgumentException("setBasePath() expects either "
                . "a string or an array as path, " . gettype($path) . " given");
        }
    }
    
    /**
     * Returns the Search Paths for pages
     *
     * @return array
     */
    public static function getSearchPath()
    {
        if (null === self::$searchPath) {
            throw new RuntimeException("Search path for pages is not defined");
        }
        return self::$searchPath;
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
    
    public function getContent()
    {
        if (null === $this->content and $this->absolutePath) {
            $textile = new Spark_View_Helper_Textile;
            
            ob_start();
            include $this->absolutePath;
            $this->content = $textile->parse(ob_get_clean());
        }
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
    
    public function setAttributes(Array $attribues)
    {
        foreach ($attributes as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }
        return $this;
    }
    
    public function getAttributes()
    {
        return $this->attributes;
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
