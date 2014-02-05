<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) ifeelweb.de
 * @version   $Id$
 * @package   
 */ 
class Ifw_Wp_Plugin_Bootstrap_Observer_Selftester extends Ifw_Wp_Plugin_Bootstrap_Observer_Abstract
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'selftester';
    }

    protected function _preBootstrap()
    {
        if ( ($this->_pm->getAccess()->isPlugin() && !$this->_pm->getAccess()->isAjax()) ||
            ($this->_pm->getAccess()->isAjax() && $this->_pm->getAccess()->hasAction('load-psn-plugin_status')) ) {

            $this->_resource = new Ifw_Wp_Plugin_Selftester($this->_pm);
        }
    }

    protected function _shutdownBootstrap()
    {
        if ( ($this->_pm->getAccess()->isPlugin() && !$this->_pm->getAccess()->isAjax()) ||
            ($this->_pm->getAccess()->isAjax() && $this->_pm->getAccess()->hasAction('load-psn-plugin_status')) ) {

            $this->_resource->activate();
        }
    }
}
