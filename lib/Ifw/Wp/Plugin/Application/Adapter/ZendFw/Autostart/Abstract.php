<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
abstract class Ifw_Wp_Plugin_Application_Adapter_ZendFw_Autostart_Abstract implements Ifw_Wp_Plugin_Application_Adapter_ZendFw_Autostart_Interface
{
    /**
     * @var Ifw_Wp_Plugin_Application_Adapter_ZendFw
     */
    protected $_adapter;

    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;



    /**
     * @param Ifw_Wp_Plugin_Application_Adapter_ZendFw $adapter
     */
    public function __construct(Ifw_Wp_Plugin_Application_Adapter_ZendFw $adapter)
    {
        $this->_adapter = $adapter;
        $this->_pm = $adapter->getPluginManager();
    }
}
