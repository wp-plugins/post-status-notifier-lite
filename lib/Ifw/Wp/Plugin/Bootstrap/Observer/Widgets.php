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
class Ifw_Wp_Plugin_Bootstrap_Observer_Widgets extends Ifw_Wp_Plugin_Bootstrap_Observer_Abstract
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'widgets';
    }

    protected function _preBootstrap()
    {
        if (Ifw_Wp_Widget_Manager::hasWidgets($this->_pm)) {
            $this->_resource = Ifw_Wp_Widget_Manager::getInstance($this->_pm);
            $this->_resource->autoload();
        }

        if (Ifw_Wp_Widget_Manager::isAccess()) {
            // load widget.js
            $widgetJsFile = 'widget.js';
            if (file_exists($this->_pm->getPathinfo()->getRootAdminJs() . $widgetJsFile)) {
                Ifw_Wp_Proxy_Script::loadAdmin('admin', $this->_pm->getEnv()->getUrlAdminJs() . $widgetJsFile);
            }
        }
    }

}
