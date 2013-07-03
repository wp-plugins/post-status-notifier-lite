<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Abstract Bootstrap
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
abstract class Ifw_Wp_Plugin_Bootstrap_Abstract implements Ifw_Wp_Plugin_Bootstrap_Interface
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
     * @var Ifw_Wp_Module_Manager
     */
    protected $_moduleManager;

    /**
     * @var Ifw_Wp_Widget_Manager
     */
    protected $_widgetManager;
    
    /**
     * @var Ifw_Wp_Plugin_Admin
     */
    protected $_admin;
    
    /**
     * @var Ifw_Wp_Plugin_Installer
     */
    protected $_installer;

    /**
     * @var Ifw_Wp_Plugin_Update_Manager
     */
    protected $_updateManager;

    /**
     * @var Ifw_Wp_Options
     */
    protected $_options;

    /**
     * @var Ifw_Wp_Options_Manager
     */
    protected $_optionsManager;

    /**
     * @var Ifw_Wp_Plugin_Selftester
     */
    protected $_selftester;
    


    /**
     * 
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public function __construct(Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
    }

    /**
     * Factorys the plugin bootstrap class
     *
     * @param Ifw_Wp_Plugin_Manager $pm the plugin manager
     * @throws Ifw_Wp_Plugin_Exception
     * @return Ifw_Wp_Plugin_Bootstrap_Abstract
     */
    public static function factory(Ifw_Wp_Plugin_Manager $pm)
    {
        $bootstrapClass = $pm->getAbbr() . '_Bootstrap';
        $bootstrapFile = $pm->getPathinfo()->getRoot() . 'bootstrap.php';

        if ((require_once $bootstrapFile) == false) {
            throw new Ifw_Wp_Plugin_Exception('Bootstrap class '. $bootstrapClass.' not found');
        }

        $bootstrap = new $bootstrapClass($pm);

        if (!($bootstrap instanceof Ifw_Wp_Plugin_Bootstrap_Abstract)) {
            throw new Ifw_Wp_Plugin_Exception('Bootstrap class '. $bootstrapClass.' must extend Ifw_Wp_Plugin_Bootstrap_Abstract');
        }

        return $bootstrap;
    }
    
    /**
     * 
     */
    public function init()
    {
        Ifw_Wp_Proxy_Action::addPluginsLoaded(array($this, 'onPluginsLoaded'));

        $this->_initOptions();
        $this->_initModules();
        $this->_initTranslation();
        $this->_initWidgets();

        if ($this->_pm->isGeneralAdminAccess()) {

            $this->_initInstaller();
            $this->_initUpdateManager();
            $this->_initSelftester();

            // init the plugin's admin pages if they are accessed
            $this->_initAdmin();
        }

        // if the request accesses the wp admin-ajax, load the ajax manager
        if (Ifw_Wp_Ajax_Manager::isAccess()) {
            $ajaxManager = Ifw_Wp_Ajax_Manager::getInstance($this->_pm->getPathinfo()->getRootAdmin());
            $ajaxManager->load($this->_pm);
        }
    }

    protected function _initSelftester()
    {
        $this->_selftester = new Ifw_Wp_Plugin_Selftester($this->_pm);
    }

    protected function _initInstaller()
    {
        $this->_installer = Ifw_Wp_Plugin_Installer::getInstance($this->_pm);
    }

    protected function _initUpdateManager()
    {
        $this->_updateManager = new Ifw_Wp_Plugin_Update_Manager($this->_pm);
        $this->_updateManager->init();
    }

    protected function _initOptions()
    {
        $this->_options = Ifw_Wp_Options::getInstance($this->_pm);
        $this->_options->init();
        Ifw_Wp_Proxy_Action::add($this->_pm->getAbbrLower() . '_after_bootstrap', array($this->_options, 'load'));
        $this->_optionsManager = new Ifw_Wp_Options_Manager($this->_pm);
    }

    /**
     * init the module manager to load on before_bootstrap action
     * this loads the modules before the main plugin bootstrap
     */
    protected function _initModules()
    {
        $this->_moduleManager = Ifw_Wp_Module_Manager::getInstance($this->_pm);
        // register module controller path before controller init
        Ifw_Wp_Proxy_Action::add($this->_pm->getAbbrLower() . '_before_controller_init', array($this->_moduleManager, 'registerModules'));
        // load modules before plugin bootstrap
        Ifw_Wp_Proxy_Action::add($this->_pm->getAbbrLower() . '_before_bootstrap', array($this->_moduleManager, 'load'));
    }

    /**
     * Checks if plugin translation files exist and inits the plugin textdomain
     *
     * @return bool
     */
    protected function _initTranslation()
    {
        $result = false;

        // load the framework translation
        Ifw_Wp_Proxy::loadTextdomain('ifw', false, $this->_pm->getPathinfo()->getDirname() . '/lib/Ifw/Wp/Translation');
        
        if (is_dir($this->_pm->getPathinfo()->getRootLang())) {
            $langRelPath = $this->_pm->getPathinfo()->getDirname() . '/lang';
            $result = Ifw_Wp_Proxy::loadTextdomain($this->_pm->getEnv()->getTextDomain(), false, $langRelPath);
        }
        return $result;
    }
    
    /**
     * Checks if the plugin has widgets and initializes them
     */
    protected function _initWidgets()
    {
        if (Ifw_Wp_Widget_Manager::hasWidgets($this->_pm)) {
            $this->_widgetManager = Ifw_Wp_Widget_Manager::getInstance($this->_pm);
            $this->_widgetManager->autoload();
        }
        
        if (Ifw_Wp_Widget_Manager::isAccess()) {
            // load widget.js
            $widgetJsFile = 'widget.js';
            if (file_exists($this->_pm->getPathinfo()->getRootAdminJs() . $widgetJsFile)) {
                Ifw_Wp_Proxy_Script::loadAdmin('admin', $this->_pm->getEnv()->getUrlAdminJs() . $widgetJsFile);
            }
        }
    }
    
    /**
     * Initializes the admin environment
     */
    protected function _initAdmin()
    {
        $this->_admin = Ifw_Wp_Plugin_Admin::getInstance($this->_pm);
        $this->_admin->autoload();
    }
    
    /**
     * Triggered when all plugins are loaded. Can be overwritten by plugin bootstrap
     */
    public function onPluginsLoaded()
    {
    }
    
    /**
     * @return Ifw_Wp_Widget_Manager
     */
    public function getWidgetManager()
    {
        return $this->_widgetManager;
    }

    /**
     * @return Ifw_Wp_Plugin_Admin
     */
    public function getAdmin()
    {
        return $this->_admin;
    }

    /**
     * @return \Ifw_Wp_Options
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @return \Ifw_Wp_Options_Manager
     */
    public function getOptionsManager()
    {
        return $this->_optionsManager;
    }

    /**
     * @return \Ifw_Wp_Plugin_Installer
     */
    public function getInstaller()
    {
        return $this->_installer;
    }

    /**
     * @return \Ifw_Wp_Plugin_Selftester
     */
    public function getSelftester()
    {
        return $this->_selftester;
    }

    /**
     * @return \Ifw_Wp_Plugin_Update_Manager
     */
    public function getUpdateManager()
    {
        return $this->_updateManager;
    }

    /**
     * @return \Ifw_Wp_Module_Manager
     */
    public function getModuleManager()
    {
        return $this->_moduleManager;
    }

}
