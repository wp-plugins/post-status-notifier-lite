<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2012-2013 ifeelweb.de
 * @version   $Id$
 * @package   
 */ 
class Ifw_Wp_Plugin_Bootstrap_Observer_Modules
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'modules';
    }

    protected function _preBootstrap()
    {
        $this->_resource = Ifw_Wp_Module_Manager::getInstance($this->_pm);

        // register module controller path before controller init
        Ifw_Wp_Proxy_Action::add($this->_pm->getAbbrLower() . '_before_controller_init', array($this->_resource, 'registerModules'));
        // load modules before plugin bootstrap
        Ifw_Wp_Proxy_Action::add($this->_pm->getAbbrLower() . '_before_bootstrap', array($this->_resource, 'load'));
    }

}
