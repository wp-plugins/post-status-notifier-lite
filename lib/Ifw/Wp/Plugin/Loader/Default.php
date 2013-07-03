<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Default loader
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
require_once dirname(__FILE__) . '/Abstract.php';

class Ifw_Wp_Plugin_Loader_Default extends Ifw_Wp_Plugin_Loader_Abstract
{
    /**
     * Inits the required resources
     */
    protected function _initResources()
    {
        $this->_resources[] = new Ifw_Wp_Plugin_Loader_Resources_IncludePath();
        $this->_resources[] = new Ifw_Wp_Plugin_Loader_Resources_Config();
        $this->_resources[] = new Ifw_Wp_Plugin_Loader_Resources_Env();
        $this->_resources[] = new Ifw_Wp_Plugin_Loader_Resources_ORM();
        $this->_resources[] = new Ifw_Wp_Plugin_Loader_Resources_PluginManager();
        $this->_resources[] = new Ifw_Wp_Plugin_Loader_Resources_Logger();
    }
    
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Loader_Abstract::load()
     */
    public function load()
    {
        parent::load();
        //$this->_setup();
    }

    /**
     * 
     */
    protected function _setup()
    {
        if ($this->_resourceStorage->has('Ifw_Wp_Plugin_Config')) {
            
            $config = $this->_resourceStorage->get('Ifw_Wp_Plugin_Config');
            
            // check for pathinfo logging
            if ($config->debug->pathinfo && $this->_resourceStorage->has('Ifw_Wp_Plugin_Logger')) {
                $this->_pluginPathinfo->setLogger($this->_resourceStorage->get('Ifw_Wp_Plugin_Logger'));
                $this->_pluginPathinfo->__toString();
            }
        };
    }

}