<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Plugin application class
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2012-2013 ifeelweb.de
 * @version   $Id$
 * @package   Ifw_Wp_Plugin_Application
 */ 
class Ifw_Wp_Plugin_Application
{
    /**
     * @var Ifw_Wp_Plugin_Application_Adapter_Interface
     */
    private $_adapter;

    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    private $_pm;



    /**
     * @param Ifw_Wp_Plugin_Application_Adapter_Interface $adapter
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    protected function __construct(Ifw_Wp_Plugin_Application_Adapter_Interface $adapter, Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_adapter = $adapter;
        $this->_pm = $pm;
    }

    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return Ifw_Wp_Plugin_Application
     */
    public static function factory(Ifw_Wp_Plugin_Manager $pm)
    {
        // so far ZendFw is default
        // this should get refactored if other frameworks will be supported
        return new self(new Ifw_Wp_Plugin_Application_Adapter_ZendFw($pm), $pm);
    }

    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return bool
     */
    public static function isAvailable(Ifw_Wp_Plugin_Manager $pm)
    {
        return is_dir($pm->getPathinfo()->getRootApplication()) === true;
    }

    /**
     * Loads the application
     */
    public function load()
    {
        Ifw_Wp_Proxy_Action::doPlugin($this->_pm, '_before_application_load', $this);

        $this->_adapter->load();

        Ifw_Wp_Proxy_Action::doPlugin($this->_pm, '_after_application_load', $this);
    }

    public function render()
    {
        $this->_adapter->render();
    }

    public function display()
    {
        $this->_adapter->display();
    }

    /**
     * @return \Ifw_Wp_Plugin_Application_Adapter_Interface
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

}
