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
class Ifw_Wp_Plugin_Bootstrap_Observer_Options extends Ifw_Wp_Plugin_Bootstrap_Observer_Abstract
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'options';
    }

    protected function _preBootstrap()
    {
        $this->_resource = Ifw_Wp_Options::getInstance($this->_pm);
        $this->_resource->init();

        Ifw_Wp_Proxy_Action::add($this->_pm->getAbbrLower() . '_after_bootstrap', array($this->_resource, 'load'));
    }

}
