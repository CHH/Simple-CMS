<?php

class Page
{
    /**
     * Search path(s) for pages and directories
     * @param array
     */
    protected static $searchPath = array();    
    
    /**
     * Name of page which gets searched if a directory within the search path is requested
     * @var string
     */
    protected static $defaultPageName = "index";
    
    /**
     * Suffix for all pages, gets stripped of the page name
     * @var string
     */
    protected static $suffix = ".txt";
    
    /**
     * Name of page without suffix
     * @var string
     */
    protected $name;
    
    /**
     * Content of page
     * @var string
     */
    protected $content;
    
    /**
     * When page was created
     * @var string
     */
    protected $created;
    
    /**
     * When page was last modified
     * @var string
     */
    protected $modified;
    
    /**
     * Attributes set at runtime
     * @param array
     */
    protected $attributes = array();
    
    /**
     * The absolute path to the page, is only set when the page gets loaded from file
     * @var string
     */
    protected $absolutePath;
    
    /**
     * Constructor
     *
     * @param  array $page key value pairs of page properties
     * @return Page
     */
    public function __construct(Array $page = array())
    {
        if ($page) {
            Spark_Options::setOptions($this, $page);
        }
    }
    
    /**
     * Find a page by name
     *
     * @param  string $file path to page, relative to the search path(s). 
     * @return Page
     */
    public static function find($file)
    {
        $page = new self;
        return $page->loadFile($file);
    }
    
    /**
     * Returns all pages from a given path
     *
     * @param  string $path Path relative to the search path(s)
     * @return array
     */
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
                or self::$suffix !== "." . pathinfo($entry->getFilename(), PATHINFO_EXTENSION)) {
                continue;
            }
            
            $page    = new self;
            $pages[] = $page->loadFile($path . DIRECTORY_SEPARATOR . $entry->getFilename());
        }
        
        return new Spark_Model_Collection($pages);
    }
    
    /**
     * Initialize the page from a given template file
     *
     * @param  string $file The filename relative to the search path(s)
     * @return Page
     */
    public function loadFile($file)
    {
        if (!is_string($file) or empty($file)) {
            throw new InvalidArgumentException("No valid file name given.");
        }
        
        if (!$absolutePath = self::getAbsolutePath($file)) {
            return false;
        }
        
        $pathinfo = pathinfo($file);
        
        $this->setName($pathinfo["filename"]);
        $this->setModified(filemtime($absolutePath));
        $this->setPath($pathinfo["dirname"]);
        
        $this->absolutePath = $absolutePath;
        
        return $this;
    }

    /**
     * Returns the absolute path for a given page or directory
     *
     * @param  string $page Either name of page or name of directory, relative to the
     *                      search path(s)
     * @return string|false
     */
    protected static function getAbsolutePath($page)
    {
        if (!is_string($page) or empty($page)) {
            throw new InvalidArgumentException("No valid file name given.");
        }
        
        $pathinfo = pathinfo($page);
        
        foreach (self::getSearchPath() as $searchPath) {
            $absoluteFilePath = $searchPath . DIRECTORY_SEPARATOR . $pathinfo["dirname"] 
                . DIRECTORY_SEPARATOR . $pathinfo["filename"];
            
            if (is_dir($absoluteFilePath)) {
                $absoluteFilePath .= DIRECTORY_SEPARATOR . self::$defaultPageName;
            }
            
            $absoluteFilePath .= self::$suffix;
            
            if (file_exists($absoluteFilePath)) {
                return $absoluteFilePath;
            }
        }
        return false;
    }
    
    /**
     * Set one or more search path(s) for pages
     *
     * @param string|array one ore more search paths
     * @return void
     */
    public static function setSearchPath($path)
    {
        if (is_string($path) and !empty($path)) {
            array_unshift(self::$searchPath, $path);
            
        } else if (is_array($path)) {
            self::$searchPath = array_merge($path, self::$searchPath);
            
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
        if (empty(self::$searchPath)) {
            throw new RuntimeException("Search path for pages is not defined");
        }
        return self::$searchPath;
    }
    
    /**
     * Set the page name, does not include the suffix
     *
     * @param  string $name
     * @return Page
     */
    public function setName($name)
    {
        if (!is_string($name) or empty($name)) {
            throw new InvalidArgumentException("setName() expects a string as name. "
                . gettype($name) . " given");
        }
        $this->name = $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * The Path in which the page file is located in
     *
     * @param  $path
     * @return Page
     */
    public function setPath($path)
    {
        if (!is_string($path) or empty($path)) {
            throw new InvalidArgumentException("setPath() expects a string as name. "
                . gettype($path) . " given");
        }
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
    
    /**
     * Lazy load the page content from the file
     *
     * @return string
     */
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
    
    public function __toString()
    {
        return $this->getContent();
    }
    
    /**
     * Set the date/time when the page was created
     *
     * @param  int|string $created
     * @return Page
     */
    public function setCreated($created)
    {
        if (is_string($created)) {
            $created = strtotime($created);
        }
        if (is_int($created)) {
            $created = date("Y-m-d H:i:s", $created);
        } else {
            throw new InvalidArgumentException("No valid date/time given");
        }
        $this->created = $created;
        return $this;
    }
    
    public function getCreated()
    {
        return $this->created;
    }
    
    /**
     * Set the date when the page was last modified
     *
     * @param  int|string $modified
     * @return Page
     */
    public function setModified($modified)
    {
        if (is_string($modified)) {
            $modified = strtotime($modified);
        }
        if (is_int($modified)) {
            $modified = date("Y-m-d H:i:s", $modified);
        } else {
            throw new InvalidArgumentException("No valid date/time given");
        }
        $this->modified = $modified;
        return $this;
    }
    
    public function getModified()
    {
        return $this->modified;
    }
    
    /**
     * Set an array of Attributes for the page
     *
     * @param  array $attributes
     * @return Page
     */
    public function setAttributes(Array $attributes)
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
    
    /**
     * Set an Attribute for the page, can be used to dynamically assign values
     * to the page
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return Page
     */
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
