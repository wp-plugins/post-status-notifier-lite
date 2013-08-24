<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Adapter to use ZendFramework as admin application
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2012-2013 ifeelweb.de
 * @version   $Id$
 * @package   Ifw_Wp_Plugin_Application
 */ 
class Ifw_Wp_Plugin_Application_Adapter_ZendFw implements Ifw_Wp_Plugin_Application_Adapter_Interface
{
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var Ifw_Zend_Application
     */
    protected $_application;

    /**
     * @var string
     */
    protected $_output;



    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public function __construct (Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
    }

    /**
     * Loads the admin application
     */
    public function load()
    {
        $this->_registerAutostart();

        $this->_application = new Ifw_Zend_Application($this->_pm->getEnv()->getEnvironmet());

        // set the dynamic options from php config file
        $this->_application->setOptions($this->_getApplicationOptions());

        // run the application bootstrap
        $this->_application->bootstrap();
    }

    /**
     *
     */
    protected function _registerAutostart()
    {
        $result = array(
            new Ifw_Wp_Plugin_Application_Adapter_ZendFw_Autostart_EnqueueScripts($this),
            new Ifw_Wp_Plugin_Application_Adapter_ZendFw_Autostart_StripSlashes($this),
            new Ifw_Wp_Plugin_Application_Adapter_ZendFw_Autostart_ZendFormTranslation($this),
        );

        foreach($result as $autostart) {
            $autostart->execute();
        }
    }

    /**
     * Retrieves the application options
     * @return array
     */
    protected function _getApplicationOptions()
    {
        $options = include $this->_pm->getPathinfo()->getRootAdminMenu() . 'configs/application.php';
        if ($this->_pm->getEnv()->getEnvironmet() == 'development') {
            $options['resources']['frontController']['params']['displayExceptions'] = 1;
            $options['phpSettings']['display_errors'] = 1;
            $options['phpSettings']['display_startup_errors'] = 1;
        }
        return $options;
    }

    /**
     * @param $controllerName
     * @param string $module
     */
    public function overwriteController($controllerName, $module = 'default')
    {
        $front = Ifw_Zend_Controller_Front::getInstance();
        $request = new IfwZend_Controller_Request_Http();

        $request->setParam('controller', $controllerName);
        $request->setParam('mod', $module);

        $front->setRequest($request);
    }

    /**
     * @return mixed|void
     */
    public function render()
    {
        // init the controller object to add actions before to load-page action (custom)
        $this->_application->initController();

        $this->_output = $this->_application->run();
    }

    /**
     * @return mixed|void
     */
    public function display()
    {
        echo $this->_output;
    }

    /**
     * @return Ifw_Wp_Plugin_Manager
     */
    public function getPluginManager()
    {
        return $this->_pm;
    }
}
