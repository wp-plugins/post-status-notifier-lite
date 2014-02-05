<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Strip slashes from $_POST values
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
class Ifw_Wp_Plugin_Application_Adapter_ZendFw_Autostart_EnqueueScripts extends Ifw_Wp_Plugin_Application_Adapter_ZendFw_Autostart_Abstract
{
    public function execute()
    {
        $this->loadCss();
        $this->loadJs();
        $this->loadSkin();
    }

    /**
     *
     */
    public function loadJs()
    {
        $adminJsFile = 'admin.js';
        if (file_exists($this->_pm->getPathinfo()->getRootAdminJs() . $adminJsFile)) {
            $handle = $this->_pm->getAbbrLower() . '-' .'admin-js';
            Ifw_Wp_Proxy_Script::loadAdmin($handle, $this->_pm->getEnv()->getUrlAdminJs() . $adminJsFile, array(), $this->_pm->getEnv()->getVersion());
        }
    }

    /**
     *
     */
    public function loadCss()
    {
        $adminCssFile = 'admin.css';
        if (file_exists($this->_pm->getPathinfo()->getRootAdminCss() . $adminCssFile)) {
            $handle = $this->_pm->getAbbrLower() . '-' .'admin';
            Ifw_Wp_Proxy_Style::loadAdmin($handle, $this->_pm->getEnv()->getUrlAdminCss() . $adminCssFile, array(), $this->_pm->getEnv()->getVersion());
        }
    }

    /**
     *
     */
    public function loadSkin()
    {
        if ($this->_pm->getEnv()->hasSkin()) {
            Ifw_Wp_Proxy_Style::loadAdmin('admin-style', $this->_pm->getEnv()->getSkinUrl() . 'style.css', array(), $this->_pm->getEnv()->getVersion());
            if ($this->_pm->hasPremium() && $this->_pm->isPremium() == false) {
                Ifw_Wp_Proxy_Style::loadAdmin('premiumad-style', $this->_pm->getEnv()->getSkinUrl() . 'premiumad.css', array(), $this->_pm->getEnv()->getVersion());
            }
        }
    }
}
