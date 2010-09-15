<?php
/**
 * @uses Zend_Loader
 */
require_once "Zend/Loader.php";

class Autoloader
{
    /**
     * Contains mappings between Prefixes and Paths
     *
     * @var array
     */
    protected $prefixDirectoryMap = array();
    
    /**
     * Path for fallback loading
     *
     * @var string
     */
    protected $basePath;
    
    const PREFIX_SEPARATOR    = "_";
    const NAMESPACE_SEPARATOR = "\\";    
    
    /**
     * Constructor
     *
     * @param  array $options key => value pairs, get inflected to Setter names
     * @return Autoloader
     */
    public function __construct(Array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Set Options on this Instance
     *
     * @param  array $options
     * @return Autoloader
     */
    public function setOptions(Array $options)
    {
        foreach ($options as $option => $value) {
            $setter = "set" . str_replace(" ", null, ucwords(str_replace("_", " ", $option)));

            if (!is_callable(array($this, $setter))) {
                throw new UnexpectedValueException("Undefined option {$option}");
            }
            
            $this->{$setter}($value);
        }
        return $this;
    }

    /**
     * Loads the given class
     *
     * @param  string $class
     * @return false|mixed False if file is not accessible, otherwise the return value
     *                     of the included file
     */
    public function autoload($class)
    {
        $prefix = substr($class, 0, strrpos($class, self::PREFIX_SEPARATOR));
        
        if (isset($this->prefixDirectoryMap[$prefix])) {
            $path     = $this->prefixDirectoryMap[$prefix];
            $class    = substr($class, strrpos($class, self::PREFIX_SEPARATOR));
            $filename = $path . $class;
        } else {
            $filename = str_replace(
                array(self::PREFIX_SEPARATOR, self::NAMESPACE_SEPARATOR), 
                DIRECTORY_SEPARATOR, 
                $class
            );

            if ($this->basePath) {
                $filename = $this->basePath . DIRECTORY_SEPARATOR . $filename;
            }
        }

        $filename .= ".php";

        if (!Zend_Loader::isReadable($filename)) {
            return false;
        }
        
        require_once $filename;
    }
    
    /**
     * Adds this autoloader on the autoloader stack
     *
     * @return Autoloader
     */
    public function register()
    {
        spl_autoload_register(array($this, "autoload"));
        return $this;
    }

    /**
     * Removes this autoloader from the autoloader stack
     *
     * @param Autoloader
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, "autoload"));
        return $this;
    }
    
    /**
     * Sets the base path for the case no Prefix => Path mapping is available for 
     * the class to load. If a relative path is set, then the include_path gets searched
     *
     * @param  string $path;
     * @return Autoloader
     */
    public function setBasePath($path)
    {
        $this->basePath = $path;
        return $this;
    }
    
    public function setPrefixes(Array $prefixes)
    {
        foreach ($prefixes as $prefix => $path) {
            $this->registerPrefix($prefix, $path);
        }
        return $this;
    }
    
    public function registerPrefix($prefix, $path)
    {
        $this->prefixDirectoryMap[$prefix] = $path;
        return $this;
    }
}
