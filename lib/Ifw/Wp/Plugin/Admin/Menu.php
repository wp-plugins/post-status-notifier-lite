<?php
/**
 * ifeelweb.de Wordpress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Admin Menu
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp_Plugin_Admin
 */
class Ifw_Wp_Plugin_Admin_Menu
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
     * @var Ifw_Zend_Application
     */
    protected $_application;
    
    /**
     * @var array
     */
    protected $_optionsPageInfo = array();
    
    /**
     * @var unknown_type
     */
    protected $_optionsPageHook;
    
    /**
     * @var array
     */
    protected $_menuPageInfo = array();
    
    /**
     * @var array
     */
    protected $_submenuPageInfo = array();
    
    /**
     * 
     * @var string
     */
    protected $_output;


    
    /**
     * Retrieves singleton Ifw_Wp_Plugin_Admin object
     *
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return Ifw_Wp_Plugin_Admin_Menu
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
     *
     */
    public function autoload()
    {
        $this->bootstrap();
    }

    /**
     * Bootstrap admin menu application
     *
     * @return bool
     */
    public function bootstrap()
    {
        if (!$this->isAvailable() || !$this->_pm->isExactAdminAccess() || Ifw_Wp_Ajax_Manager::isAccess()) {
            // no need to bootstrap if menu not available or not accessed
            return false;
        }

        $this->_registerAutostart();

        Ifw_Wp_Proxy::doAction($this->_pm->getAbbrLower() . '_before_menu_bootstrap', $this);
  
        $this->_application = new Ifw_Zend_Application($this->_pm->getEnv()->getEnvironmet());
    
        // set the dynamic options from php config file
        $this->_application->setOptions($this->_getApplicationOptions());

        Ifw_Wp_Proxy::doAction($this->_pm->getAbbrLower() . '_before_menu_application_bootstrap', $this);
        // run the application bootstrap
        $this->_application->bootstrap();
        // init the controller object to add actions before to load-page action (custom)
        $this->_application->initController();

        Ifw_Wp_Proxy::doAction($this->_pm->getAbbrLower() . '_after_menu_application_bootstrap', $this);
        Ifw_Wp_Proxy::doAction($this->_pm->getAbbrLower() . '_after_menu_bootstrap', $this);

        return true;
    }

    /**
     *
     */
    protected function _registerAutostart()
    {
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Menu' . DIRECTORY_SEPARATOR . 'Autostart';
        $dirScanner = new Ifw_Util_Directory_Scanner($dir);

        $result = $dirScanner->getClassesExtending('Ifw_Wp_Plugin_Admin_Menu_Autostart_Abstract');

        foreach($result->getObjects($this) as $autostart) {
            $autostart->execute();
        }
    }
    
    /**
     * Retrieves the application options
     * 
     * @return array
     */
    protected function _getApplicationOptions()
    {
        $options = include $this->_pm->getPathinfo()->getRootAdminMenu() . 'configs/application.php';
        if ($this->_pm->getEnv()->getEnvironmet() == 'development') {
            $options['resources']['frontController']['params']['displayExceptions'] = 1;
            $options['phpSettings']['display_errors'] = 1;
            $options['phpSettings']['display_startup_errors'] = 1;
        }
        return $options;        
    }

    /**
     * Adds an option page link to the options menu
     *
     * @param string $page_title
     * @param bool|string $menu_title
     * @param bool $onLoadCallback
     * @param string $capability
     */
    public function addOptionsPage($page_title, $menu_title=false, $onLoadCallback=false, $capability='edit_posts')
    {
        $this->_optionsPageInfo[] = array(
            'page_title' => $page_title, 
            'menu_title' => $menu_title, 
            'function' => array($this, 'displayOptionsPage'),
            'onLoadCallback' => $onLoadCallback,
            'capability' => $capability
        );
        Ifw_Wp_Proxy_Action::addAdminMenu(array($this, '_addOptionsPage'));
    }
    
    /**
     * Used as callback by self::addOptionsPage()
     */    
    public function _addOptionsPage()
    {
        foreach ($this->_optionsPageInfo as $page) {
            $page_title = $page['page_title'];
            if ($page['menu_title'] == false) {
                $menu_title = $page_title;
            } else {
                $menu_title = $page['menu_title'];
            }
            $this->_optionsPageHook = add_options_page($page_title, $menu_title, $page['capability'], $this->_pm->getPathinfo()->getDirname(), $page['function']);

            if ($this->_pm->isExactAdminAccess() && $this->_application->hasController()) {
                // add onLoad action to controller
                Ifw_Wp_Proxy_Action::add('load-'. $this->_optionsPageHook, array($this->_application->getController(), 'onLoad'));
            }

            Ifw_Wp_Proxy_Action::add('load-'. $this->_optionsPageHook, array($this, 'renderOptionsPage'));

            if (is_callable($page['onLoadCallback'], true, $callable_name)) {
                Ifw_Wp_Proxy_Action::add('load-'. $this->_optionsPageHook, $page['onLoadCallback']);
            }
        }
    }

    /**
     * Adds a menu item to the WP admin menu
     *
     * @param string $menu_title
     * @param string $page_title
     * @param string $menu_slug
     * @param callable|string $function
     * @param string $icon_url
     * @param string $capability
     * @param int $position
     * @return string $menu_slug
     */
    public function addMenuPage($menu_title, $page_title='', $menu_slug='', $function='', $icon_url='', $capability='', $position=null) 
    {
        $menu_slug = ($menu_slug == '') ? $this->_pm->getPathinfo()->getDirname() : $menu_slug;
        
        $this->_menuPageInfo[] = array(
            'menu_title' => $menu_title,
            'page_title' => ($page_title == '') ? $menu_title : $page_title,
            'menu_slug' => $menu_slug,
            'function' => ($function == '') ? array($this, 'displayOptionsPage') : $function,
            'icon_url' => $icon_url,
            'capability' => ($capability == '') ? 'edit_posts' : $capability,
            'position' => $position
        );
        
        Ifw_Wp_Proxy_Action::add('admin_menu', array($this, '_addMenuPage'));
        return $menu_slug;
    }
    
    /**
     * Used as callback function by self::addMenuPage()
     */
    public function _addMenuPage()
    {
        foreach ($this->_menuPageInfo as $page) {
            $this->_optionsPageHook = add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], 
                $page['menu_slug'], $page['function'], $page['icon_url'], $page['position']);
            Ifw_Wp_Proxy_Action::add('load-'. $this->_optionsPageHook, array($this, 'renderOptionsPage'));
        }
    }

    /**
     * Adds a submenu item to a menu created by self::addMenuPage()
     *
     * @param string $parent_slug
     * @param string $menu_title
     * @param string $menu_slug
     * @param callable|string $function
     * @param string $page_title
     * @param string $capability
     */
    public function addSubmenuPage($parent_slug, $menu_title, $menu_slug, $function='', $page_title='', $capability='')
    {
        $this->_submenuPageInfo[] = array(
            'parent_slug' => $parent_slug,
            'menu_title' => $menu_title,
            'menu_slug' => $menu_slug,
            'function' => ($function == '') ? array($this, 'displayOptionsPage') : $function,
            'page_title' => ($page_title == '') ? $menu_title : $page_title,
            'capability' => ($capability == '') ? 'edit_posts' : $capability
        );
        
        Ifw_Wp_Proxy_Action::add('admin_menu', array($this, '_addSubmenuPage'));
    }
    
    /**
     * Used as callback function by self::addSubmenuPage()
     */
    public function _addSubmenuPage()
    {
        foreach ($this->_submenuPageInfo as $page) {
            $this->_optionsPageHook = add_submenu_page($page['parent_slug'], $page['page_title'], $page['menu_title'], 
                    $page['capability'], $page['menu_slug'], $page['function']);
            Ifw_Wp_Proxy_Action::add('load-'. $this->_optionsPageHook, array($this, 'renderOptionsPage'));
        }
    }

    /**
     *
     * @param $helptext
     * @param string|\unknown_type $title
     * @param string $sidebar
     * @internal param \unknown_type $help
     */
    public function addHelp($helptext, $title='', $sidebar='')
    {
        $help = new Ifw_Wp_Plugin_Admin_Menu_Help($this->_pm);
        $help->setTitle($title)->setHelp($helptext)->setSidebar($sidebar)->load();
    }

    /**
     * Checks if plugin has a admin/menu directory
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return is_dir($this->_pm->getPathinfo()->getRootAdminMenu()) === true;
    }
    
    /**
     * Checks if admin menu of plugin is accessed
     *
     * @return boolean
     */
    public function isAccess()
    {
        if (isset($_GET['page']) && 
            strstr($_GET['page'], $this->_pm->getPathinfo()->getDirname())) {
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @return string
     */
    public function getOptionsPagePath()
    {
        return 'options-general.php?page=' . $this->_pm->getPathinfo()->getDirname();
    }

    /**
     * @param $controller
     * @param string $action
     * @param null $page
     * @param array $extra
     * @return string
     */
    public function getUrl($controller, $action='index', $page=null, $extra = array())
    {
        if ($page == null) {
            $page = $this->_pm->getPathinfo()->getDirname();
        }

        $urlOptions = array_merge(array(
            'controller' => $controller,
            'action' => $action,
            'page' => $page
        ), $extra);

        $router = $this->_pm->getAdminFrontController()->getRouter();
        return $router->assemble($urlOptions, 'requestVars');
    }

    /**
     * 
     * @return string
     */
    public function getBaseUrl()
    {
        // TODO for menu/submenu
        return 'options-general.php';
    }
    
    /**
     * @return Ifw_Zend_Application
     */
    public function getApplication()
    {
        return $this->_application;
    }
    
    /**
     * @return the $_optionsPageHook
     */
    public function getOptionsPageHook()
    {
        return $this->_optionsPageHook;
    }

    /**
     * @return array
     */
    public function getOptionsPageInfo()
    {
        return $this->_optionsPageInfo;
    }

    /**
     * @return array
     */
    public function getMenuPageInfo()
    {
        return $this->_menuPageInfo;
    }

    /**
     * @return array
     */
    public function getSubmenuPageInfo()
    {
        return $this->_submenuPageInfo;
    }

    /**
     * @return \Ifw_Wp_Plugin_Manager
     */
    public function getPm()
    {
        return $this->_pm;
    }

    /**
     *
     */
    public function renderOptionsPage()
    {
        // Frontcontroller is set to return response
        $this->_output = $this->_application->run();
    }
    
    /**
     *
     */
    public function displayOptionsPage()
    {
        echo $this->_output;
    }

    public function setController($controller)
    {
        $this->_controller = $controller;
    }

    public function getController()
    {
        return $this->_controller;
    }



}
