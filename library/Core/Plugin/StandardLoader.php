<?php
/**
 * Standard implemenation of a plugin loader
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
namespace Core\Plugin;

class StandardLoader implements Loader
{
    /** @var string */
    protected $path;
    
    /** @var Environment */
    protected $environment;
    
    protected $namespace = "Plugin";

    function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    function setOptions(array $options)
    {
        \Spark\Util\Options::setOptions($this, $options);
        return $this;
    }

    function loadAll() 
    {
        $pluginPath     = $this->getPath();
        $pluginIterator = new \DirectoryIterator($pluginPath);
        $exceptions     = new ExceptionStack;
        
        foreach($pluginIterator as $entry) { 
            if($entry->isDir() and !$entry->isDot()) {
                $plugin = $entry->getFilename();
                try {
                    $this->load($plugin);
                } catch (\Exception $e) {
                    $exceptions->push($e);
                }
            }
        }
        
        if (count($exceptions) > 0) {
            throw $exceptions;
        }
        
        return $this;
    }

    function load($pluginName)
    { 
        $env = $this->environment;
        
        if ($env->isRegistered($pluginName)) return $env->getPlugin($pluginName);
        
        $ds = DIRECTORY_SEPARATOR;
        
        $pluginBootstrapFile = realpath($this->getPath() . $ds . $pluginName . $ds 
            . $pluginName . ".php");
        
        if(!$pluginBootstrapFile) {
            throw new Exception(sprintf(
                "The plugin %s was not found in %s, please make sure its correctly installed",
                $pluginName,
                $this->getPath() . $ds . $pluginName
            ));
        }
        include_once($pluginBootstrapFile);
        
        $className = "\\" . $this->namespace . "\\" . $pluginName;
        
        $plugin = new $className;

        if (!$plugin instanceof Plugin) {
            throw new Exception("Plugins must implement the Core\Plugin\Plugin Interface");
        }

        /*
         * If Plugin extends the Abstract Plugin, then give it some more information
         */
        if ($plugin instanceof AbstractPlugin) {
            $plugin->setPath($this->getPath() . $ds . $pluginName);
            $plugin->setEnvironment($env);
        }

        try {
            $plugin->init();

        } catch(\Exception $e) {
            throw new Exception(sprintf(
                "There was an exception while bootstrapping the plugin %s",
                $pluginName
            ), null, $e);
        }
        
        $env->registerPlugin($pluginName, $plugin);
        return $plugin;
    }

    function setEnvironment(Environment $env)
    {
        $this->environment = $env; 
        $env->setLoader($this);
        Controller::setEnvironment($env);
        return $this;
    }
    
    function getEnvironment()
    {
        return $this->environment;
    }
    
    function setPath($pluginPath)
    {
        $this->path = $pluginPath;
        return $this;
    }
    
    function getPath()
    {
        if(!is_null($this->path)) {
            return $this->path;
        }
        throw new \UnexpectedValueException("Please set the plugin path correctly before you attempt to load plugins");
    }
}
