<?php
/**
 * Overwrites Zend_Application constructor for disabling the Zend Autoloader
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
class Ifw_Zend_Application extends IfwZend_Application
{
    /**
     * Constructor
     *
     * Initialize application. Potentially initializes include_paths, PHP
     * settings, and bootstrap class.
     *
     * @param  string $environment
     * @param  string|array|IfwZend_Config $options String path to configuration file, or array/IfwZend_Config of configuration options
     * @throws IfwZend_Application_Exception
     * @return \Ifw_Zend_Application
     */
    public function __construct($environment, $options = null)
    {
        $this->_environment = (string) $environment;
    
//        require_once 'IfwZend/Loader/Autoloader.php';
//         $this->_autoloader = IfwZend_Loader_Autoloader::getInstance();
    
        if (null !== $options) {
            if (is_string($options)) {
                $options = $this->_loadConfig($options);
            } elseif ($options instanceof IfwZend_Config) {
                $options = $options->toArray();
            } elseif (!is_array($options)) {
                throw new IfwZend_Application_Exception('Invalid options provided; must be location of config file, a config object, or an array');
            }
    
            $this->setOptions($options);
        }
    }

    public function initController()
    {
        // init the controller
        $pm = $this->getOption('pluginmanager');
//        Ifw_Wp_Proxy_Action::doPlugin($pm, 'before_controller_init', $this);
        return $this->getBootstrap()->initController();
    }

    public function run()
    {
        return $this->getBootstrap()->run();
    }

    /**
     * @return null|IfwZend_Controller_Action_Interface
     */
    public function getController()
    {
        $front = $this->getBootstrap()->getResource('FrontController');
        return $front->getDispatcher()->getController();
    }

    public function hasController()
    {
        $front = $this->getBootstrap()->getResource('FrontController');
        return $front->getDispatcher()->getController() instanceof IfwZend_Controller_Action_Interface;
    }
}
