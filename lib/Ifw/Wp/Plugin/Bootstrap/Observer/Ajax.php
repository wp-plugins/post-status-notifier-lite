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
class Ifw_Wp_Plugin_Bootstrap_Observer_Ajax extends Ifw_Wp_Plugin_Bootstrap_Observer_Abstract
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'ajax';
    }

    protected function _preBootstrap()
    {
        if ($this->_pm->getAccess()->isAjax() && !$this->_pm->getAccess()->isHeartbeat()) {
            $this->_resource = Ifw_Wp_Ajax_Manager::getInstance($this->_pm->getPathinfo()->getRootAdmin());
            $this->_resource->load($this->_pm);
        }
    }

    protected function _shutdownBootstrap()
    {
    }
}
