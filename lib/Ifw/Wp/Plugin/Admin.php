<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Admin Menu
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp_Plugin_Admin
 */
class Ifw_Wp_Plugin_Admin
{
    /**
     * Instance store
     * @var array
     */
    public static $_instances = array();
    
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;
    
    /**
     * @var Ifw_Wp_Plugin_Admin_Menu
     */
    protected $_menu;
    
    
    
    /**
     * Retrieves singleton Ifw_Wp_Plugin_Admin object
     * 
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return Ifw_Wp_Plugin_Admin
    */
    public static function getInstance(Ifw_Wp_Plugin_Manager $pm)
    {
        if (!isset(self::$_instances[$pm->getAbbr()])) {
            self::$_instances[$pm->getAbbr()] = new self($pm);
        }
        return self::$_instances[$pm->getAbbr()];
    }
    
    /**
     * 
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    protected function __construct (Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
    }
    
    /**
     * Autoloads the admin environment of the plugin
     * 
     */
    public function autoload()
    {
        if ($this->_pm->isExactAdminAccess()) {
            $this->enqueueScripts();
        }

        // menu gets instantiated always for common use. application only gets launched on exact access
        $this->_menu = Ifw_Wp_Plugin_Admin_Menu::getInstance($this->_pm);
        $this->_menu->autoload();
    }

    /**
     * Enqueues the admin css/js
     */
    public function enqueueScripts()
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
            Ifw_Wp_Proxy_Script::loadAdmin($handle, $this->_pm->getEnv()->getUrlAdminJs() . $adminJsFile);
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
            Ifw_Wp_Proxy_Style::loadAdmin($handle, $this->_pm->getEnv()->getUrlAdminCss() . $adminCssFile);
        }
    }
    
    /**
     *
     */
    public function loadSkin()
    {
        if ($this->_pm->getEnv()->hasSkin()) {
            Ifw_Wp_Proxy_Style::loadAdmin('admin-style', $this->_pm->getEnv()->getSkinUrl() . 'style.css');
            if ($this->_pm->hasPremium() && $this->_pm->isPremium() == false) {
                Ifw_Wp_Proxy_Style::loadAdmin('premiumad-style', $this->_pm->getEnv()->getSkinUrl() . 'premiumad.css');
            }
        }
    }
    
    /**
     * Registers a callback to render plugin action links in WP plugin menu
     * 
     * @param callback $callback
     */
    public function addPluginMenuActionLinks($callback)
    {
        if (self::isAccess()) {
            Ifw_Wp_Proxy_Filter::add('plugin_action_links_'. $this->_pm->getPathinfo()->getFilenamePath(), $callback, 10, 2);
        }
    }
    
    /**
     * Checks if WP admin section is accessed
     * Excludes AJAX requests
     *
     * @return boolean
     */
    public static function isAccess()
    {
        return is_admin() && !Ifw_Wp_Ajax_Manager::isAccess();
    }
    
    /**
     * @return Ifw_Wp_Plugin_Admin_Menu
     */
    public function getMenu()
    {
        return $this->_menu;
    }

}
