<?php

namespace Plugin\Pages;

use InvalidArgumentException,
    DirectoryIterator,
    SplStack,
    Textile,
    Spark\Options,
    Spark\Util\ArrayObject,
    Phly\Mustache\Mustache;

class Page
{   
    /**
     * Name of page which gets searched if a directory within the search path is requested
     * @var string
     */
    protected static $defaultPageName = "index";
    
    /**
     * Suffix for all pages, gets stripped off the page name
     * @var string
     */
    protected static $suffix = ".txt";
    
    /** @var Phly\Mustache\Mustache */
    protected static $mustache;
    
    /** @var SplStack */
    protected static $searchPath;

    /**
     * Name of page without suffix
     * @var string
     */
    public $name;
    
    /**
     * When page was created
     * @var string
     */
    public $created;
    
    /**
     * When page was last modified
     * @var string
     */
    public $modified;

    /**
     * Attributes set at runtime
     * @param array
     */
    public $attributes = array();
    
    public $filename;
    
    /**
     * Content of page
     * @var string
     */
    protected $content;
    
    /** @var bool */
    protected $isRendered = false;
    
    /**
     * Constructor
     *
     * @param  array $page key value pairs of page properties
     * @return Page
     */
    function __construct(Array $page = array())
    {
        if ($page) {
            Options::setOptions($this, $page);
        }
    }
    
    /**
     * Find a page by name
     *
     * @param  string $file path to page, relative to the search path(s). 
     * @return Page
     */
    static function find($file)
    {
        $page = new self;
        return $page->fromFile($file);
    }
    
    /**
     * Returns all pages from a given path
     *
     * @param  string $path Path relative to the search path(s)
     * @return array
     */
    static function findAll($path) 
    {
        if (!is_string($path) or empty($path)) {
            throw new InvalidArgumentException("No path given");
        }
        
        $directory = new DirectoryIterator($path);
        $pages     = array(); 
        
        foreach ($directory as $entry) {
            if (!$entry->isFile() or $entry->isDot() 
                or self::$suffix !== "." . pathinfo($entry->getFilename(), PATHINFO_EXTENSION)) {
                continue;
            }
            
            $page    = new self;
            $pages[] = $page->fromFile($path . DIRECTORY_SEPARATOR . $entry->getFilename());
        }
        
        return new ArrayObject($pages);
    }
    
    protected function search($file)
    {
        foreach (static::$searchPath as $path) {
            $template = $path . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($template)) {
                $template .= DIRECTORY_SEPARATOR . "index" . static::$suffix;
            }
            
            if (substr($template, strlen(static::$suffix), -strlen(static::$suffix)) !== static::$suffix) {
                $template .= static::$suffix;
            }
            
            if (file_exists($template)) {
                return $template;
            }
        }
        return false;
    }
    
    /**
     * Initialize the page from a given template file
     *
     * @param  string $file The filename relative to the search path(s)
     * @return Page
     */
    function fromFile($file)
    {
        if (!is_string($file) or empty($file)) {
            throw new InvalidArgumentException("No valid file name given.");
        }
        
        $template = $this->search($file);
        
        if (!$template) {
            return false;
        }
        $this->filename = $template;

        $pathinfo = pathinfo($template);
        $this->setName($pathinfo["filename"] ?: "index");
        $this->setModified(filemtime($template));
        $this->setPath($pathinfo["dirname"]);
        
        return $this;
    }
    
    /**
     * Set one or more search path(s) for pages
     *
     * @param string|array one ore more search paths
     * @return void
     */
    static function setSearchPath($path)
    {
        if (is_array($path)) {
            foreach ($path as $p) {
                static::setSearchPath($p);
            }
            return true;
        }
        if (null === static::$searchPath) {
            static::$searchPath = new SplStack;
        }
        static::$searchPath->push($path);
    }
    
    /**
     * Set the page name, does not include the suffix
     *
     * @param  string $name
     * @return Page
     */
    function setName($name)
    {
        if (!is_string($name) or empty($name)) {
            throw new InvalidArgumentException("setName() expects a string as name. "
                . gettype($name) . " given");
        }
        $this->name = $name;
        return $this;
    }
    
    function getName()
    {
        return $this->name;
    }
    
    /**
     * The Path in which the page file is located in
     *
     * @param  $path
     * @return Page
     */
    function setPath($path)
    {
        if (!is_string($path) or empty($path)) {
            throw new InvalidArgumentException("setPath() expects a string as name. "
                . gettype($path) . " given");
        }
        $this->path = $path;
        return $this;
    }
    
    function getPath()
    {
        return $this->path;
    }
    
    function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
    
    /**
     * Lazy load the page content from the file
     *
     * @return string
     */
    function getContent()
    {
        if (false === $this->isRendered) {
            $textile  = new Textile;
            $mustache = static::getMustache();
            $tokens   = $mustache->getLexer()->compile(file_get_contents($this->filename));
            $content  = $mustache->getRenderer()->render($tokens, $this);
            
            $this->content    = $textile->TextileThis($content);
            $this->isRendered = true;
        }
        return $this->content;
    }
    
    function __toString()
    {
        return $this->getContent();
    }
    
    /**
     * Set the date/time when the page was created
     *
     * @param  int|string $created
     * @return Page
     */
    function setCreated($created)
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
    
    function getCreated()
    {
        return $this->created;
    }
    
    /**
     * Set the date when the page was last modified
     *
     * @param  int|string $modified
     * @return Page
     */
    function setModified($modified)
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
    
    function getModified()
    {
        return $this->modified;
    }
    
    protected static function getMustache()
    {
        if (null === static::$mustache) {
            static::$mustache = new Mustache;
            static::$mustache->setSuffix(static::$suffix);
        }
        return static::$mustache;
    }
    
    /**
     * Set an array of Attributes for the page
     *
     * @param  array $attributes
     * @return Page
     */
    function setAttributes(Array $attributes)
    {
        foreach ($attributes as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }
        return $this;
    }
    
    function getAttributes()
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
    function setAttribute($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
        return $this;
    }
    
    function getAttribute($attribute)
    {
        if (!isset($this->attributes[$attribute])) {
            return null;
        }
        return $this->attributes[$attribute];
    }
}
