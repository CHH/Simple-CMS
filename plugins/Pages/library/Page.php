<?php
/**
 * Page Class
 *
 * Represents a page and handles rendering through Textile and Mustache
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @package    Plugin
 * @subpackage Pages
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
namespace Plugin\Pages;

use InvalidArgumentException,
    DirectoryIterator,
    SplStack,
    Textile,
    Spark\Util\Options,
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

	/** @var Textile */
	protected static $textile;
    
    /** @var SplStack */
    protected static $searchPath;

    /**
     * Name of page without suffix
     * @var string
     */
    protected $name;
    
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
    
    protected $filename;
    
    /**
     * Rendered Content of page
     * @var string
     */
    protected $content;

    /** @var string content of page, not rendered */
    protected $rawContent;
    
    /** @var bool */
    protected $isRendered = false;

    protected $renderTextile = true;
    
    /**
     * Constructor
     *
     * @param  array $page key value pairs of page properties
     * @return Page
     */
    function __construct(Array $page = array())
    {
        if ($page) {
            $this->fromArray($page);
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
        
        if (!$file = static::search($file)) return false;
        else return $page->fromFile($file);
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

    static function setDefaultPageName($name = "index")
    {
        static::$defaultPageName = $name;
    }
    
    /**
     * Returns all pages from a given path
     *
     * @param  string $path Path relative to the search path(s)
     * @return array
     */
    static function findAll($path, $number = null, $offset = null) 
    {
        if (!is_string($path) or empty($path)) {
            throw new InvalidArgumentException("No path given");
        }
        
        $path = rtrim($path, "/\\");
        
        foreach (static::$searchPath as $p) {
            if (is_dir($p .= DIRECTORY_SEPARATOR . $path)) {
                $path = $p;
                break;
            }
        }
        
        $directory = new DirectoryIterator($path);
        $pages     = array(); 
        
        if (null !== $offset) {
            $directory->seek($offset);

            if (!$directory->valid()) {
                throw new InvalidArgumentException("$offset is not valid");
            }
        }
        
        foreach ($directory as $entry) {
            if (!$entry->isFile() or $entry->isDot() 
                or self::$suffix !== "." . pathinfo($entry->getFilename(), PATHINFO_EXTENSION)) {
                continue;
            }
            if ("index" . static::$suffix === $entry->getFilename()) {
                continue;
            }
            $page    = new self;
            $pages[] = $page->fromFile($path . DIRECTORY_SEPARATOR . $entry->getFilename());

            if (null !== $number and strlen($pages) === $number) {
                break;
            }
        }
        return new ArrayObject($pages);
    }
    
    protected static function search($file)
    {
        foreach (static::$searchPath as $path) {
            $template = $path . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($template)) {
                $template .= DIRECTORY_SEPARATOR . static::$defaultPageName . static::$suffix;
            }
            
            if (false === strpos($template, static::$suffix)) {
                $template .= static::$suffix;
            }
            
            if (file_exists($template)) {
                return $template;
            }
        }
        return false;
    }
    
    function fromArray(Array $data)
    {
        Options::setOptions($this, $data);
        return $this;
    }
    
    function toArray()
    {
        return array(
            "name"       => $this->name,
            "content"    => $this->rawContent,
            "attributes" => $this->attributes,
            "created"    => $this->created,
            "modified"   => $this->modified
        );
    }

    function toString()
    {
        return $this->getContent();
    }
    
    /**
     * Initialize the page from a given template file
     *
     * @param  string $file The filename relative to the search path(s)
     * @return Page
     */
    function fromFile($file)
    {
        if (!file_exists($file)) return false;
        else $this->filename = $file;
        
        $pathinfo = pathinfo($file);
        $this->setName($pathinfo["filename"] ?: "index");
        $this->setModified(filemtime($file));
        $this->setPath($pathinfo["dirname"]);
        
        $this->rawContent = file_get_contents($file);
        
        return $this;
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
        $this->rawContent = $content;
        $this->isRendered = false;
        return $this;
    }
    
    /**
     * Renders the page's content through Mustache and Textile. Returns the 
     * rendered content.
     *
     * @return string
     */
    function getContent()
    {
        if (false === $this->isRendered) {
            $textile  = static::getTextile();
            $mustache = static::getMustache();
            
            $tokens   = $mustache->getLexer()->compile($this->rawContent);
            $content  = $mustache->getRenderer()->render($tokens, $this);

            if ($this->renderTextile) {
                $content = $textile->TextileThis($content);
            }
            $this->content = $content;
            $this->isRendered = true;
        }
        return $this->content;
    }
    
    function getRawContent()
    {
        return $this->rawContent;
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
    
    function setRenderTextile($enabled = true)
    {
        $this->renderTextile = $enabled;
        return $this;
    }
    
    function __set($var, $value)
    {
        $setter = "set" . ucfirst($var);
        if (!is_callable(array($this, $setter))) {
            $this->{$var} = $value;
            return;
        }
        $this->{$setter}($value);
    }
    
    function __get($var)
    {
        $getter = "get" . ucfirst($var);
        if (!is_callable(array($this, $getter))) {
            throw new \Exception("$var is not defined");
        }
        return $this->{$getter}();
    }
    
    /**
     * Returns an instance of the Textile parser
     *
     * @return Textile
     */
    static function getTextile()
    {
        if (null === static::$textile) {
            static::$textile = new Textile;
        }
        return static::$textile;
    }
    
    /**
     * Return a configured instance of Mustache
     *
     * Important: the page class uses its own loading mechanism instead of Mustache's.
     *
     * @return Phly\Mustache\Mustache
     */
    static function getMustache()
    {
        if (null === static::$mustache) {
            $mustache = new Mustache;
            
            $mustache->getRenderer()
                ->addPragma(new \Phly\Mustache\Pragma\ImplicitIterator)
                ->addPragma(new \Plugin\Pages\Pragma\FormatDate);
            
            static::$mustache = $mustache;
        }
        return static::$mustache;
    }
}
