<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Abstract loader
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
require_once dirname(__FILE__) . '/Interface.php';

abstract class Ifw_Wp_Plugin_Loader_Abstract implements Ifw_Wp_Plugin_Loader_Interface
{
    /**
     * @var Ifw_Wp_Pathinfo_Plugin
     */
    protected $_pluginPathinfo;
    
    /**
     * @var Ifw_Wp_Plugin_Loader_ResourceStorage
     */
    protected $_resourceStorage;
    
    /**
     * @var array
     */
    protected $_resources = array();
    
    
    /**
     * @param string $pathinfo
     */
    public function __construct($pathinfo)
    {
        $this->_initPathinfo($pathinfo);
        $this->_initResourceStorage();        
        $this->_initAutoloader();        
        $this->_initResources();
    }
    
    /**
     * Inits the pathinfo object
     */
    protected function _initPathinfo($pathinfo)
    {
        if (!class_exists('Ifw_Wp_Pathinfo_Plugin')) {
            require_once dirname(__FILE__) . '/../../Pathinfo/Abstract.php';
            require_once dirname(__FILE__) . '/../../Pathinfo/Plugin.php';
        }

        $this->_pluginPathinfo = new Ifw_Wp_Pathinfo_Plugin($pathinfo);
    }
    
    /**
     * 
     */
    protected function _initResourceStorage()
    {
        if (!class_exists('Ifw_Wp_Plugin_Loader_ResourceStorage')) {
            require_once $this->_pluginPathinfo->getRootLib() . 'Ifw/Wp/Plugin/Loader/ResourceStorage.php';
        }
        $this->_resourceStorage = new Ifw_Wp_Plugin_Loader_ResourceStorage();
        $this->_resourceStorage->add($this->_pluginPathinfo);
    }
    
    /**
     * Loads autoloader before other resources for convenience
     */
    protected function _initAutoloader()
    {
        require_once dirname(__FILE__) . '/Resources/Interface.php';
        require_once dirname(__FILE__) . '/Resources/Autoloader.php';
        $resource = new Ifw_Wp_Plugin_Loader_Resources_Autoloader();
        $resource->load($this->_resourceStorage);
    }

    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Loader_Interface::load()
     */
    public function load()
    {
        foreach ($this->_resources as $resource) {
            $resource->load($this->_resourceStorage);
        }
    }

    /**
     * @throws Ifw_Wp_Plugin_Loader_Exception
     * @return Ifw_Wp_Plugin_Logger
     */
    public function getLogger()
    {
        if (!$this->_resourceStorage->has('Ifw_Wp_Plugin_Logger')) {
            throw new Ifw_Wp_Plugin_Loader_Exception('Could not retrieve logger object');
        }
    
        return $this->_resourceStorage->get('Ifw_Wp_Plugin_Logger');
    }

    /**
     * @throws Ifw_Wp_Plugin_Loader_Exception
     * @return Ifw_Wp_Plugin_Manager
     */
    public function getPluginManager()
    {
        if (!$this->_resourceStorage->has('Ifw_Wp_Plugin_Manager')) {
            throw new Ifw_Wp_Plugin_Loader_Exception('Could not retrieve plugin manager object');
        }
    
        return $this->_resourceStorage->get('Ifw_Wp_Plugin_Manager');
    }
    
    /**
     * @throws Ifw_Wp_Plugin_Loader_Exception
     * @return Ifw_Wp_Env_Plugin
     */
    public function getEnv()
    {
        if (!$this->_resourceStorage->has('Ifw_Wp_Env_Plugin')) {
            throw new Ifw_Wp_Plugin_Loader_Exception('Could not retrieve environment object');
        }

        return $this->_resourceStorage->get('Ifw_Wp_Env_Plugin');
    }
    
    /**
     * Concrete loader must overwrite to init resources
     */
    protected abstract function _initResources();
}
