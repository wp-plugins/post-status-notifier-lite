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
    }

    protected function _postBootstrap()
    {
        $this->_resource->load();
    }

}
