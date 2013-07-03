<?php
/**
 * ifeelweb.de Wordpress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Plugin Manager
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2012-2013 ifeelweb.de
 * @version   $Id$
 * @package   Ifw_Wp_Plugin
 */
class Ifw_Wp_Plugin_Manager
{
    /**
     * Stores plugin manager objects
     * @var array
     */
    protected static $_instances = array();

    /**
     * Plugin abbreviation
     * @var string
     */
    protected $_abbr;
    
    /**
     * @var Ifw_Wp_Pathinfo_Plugin
     */
    protected $_pathinfo;
    
    /**
     * Abbreviation character length
     * @var int
     */
    protected static $_defaultAbbrLength = 3;

    /**
     * @var Ifw_Wp_Plugin_Config
     */
    protected $_config;

    /**
     * @var Ifw_Wp_Plugin_Bootstrap_Abstract
     */
    protected $_bootstrap;



    /**
     * Initializes the plugin manager
     *
     * @param Ifw_Wp_Pathinfo_Plugin $pluginPathinfo
     * @param bool|\false|string $abbr
     * @return Ifw_Wp_Plugin_Manager
     */
    public static function init(Ifw_Wp_Pathinfo_Plugin $pluginPathinfo, $abbr=false)
    {
        if (!is_string($abbr)) {
            $abbr = self::_createAbbr($pluginPathinfo->getFilename());
        }
        
        if (!isset(self::$_instances[$abbr])) {
            self::$_instances[$abbr] = new self($abbr, $pluginPathinfo);
        }
        
        return self::getInstance($abbr);
    }

    /**
     * Retrieves singleton instance of Ifw_Wp_Plugin_Manager
     *
     * @param string
     * @throws Ifw_Wp_Plugin_Exception
     * @return Ifw_Wp_Plugin_Manager
     */
    public static function getInstance($abbr)
    {      
        if (!isset(self::$_instances[$abbr])) {
            throw new Ifw_Wp_Plugin_Exception('No instance stored under '. $abbr);
        }
        
        return self::$_instances[$abbr];
    }

    /**
     * Checks if an instance is stored to an abbreviation
     * @param $abbr
     * @return bool
     */
    public static function hasInstance($abbr)
    {
        return isset(self::$_instances[$abbr]);
    }

    /**
     * @param $filenamePath
     * @return Ifw_Wp_Plugin_Manager|null
     */
    public static function getInstanceFromFilenamePath($filenamePath)
    {
        $filenameWithExtension = array_pop(explode(DIRECTORY_SEPARATOR, $filenamePath));
        $filename = array_shift(explode('.', $filenameWithExtension));
        $abbr = self::_createAbbr($filename);

        if (self::hasInstance($abbr)) {
            return self::getInstance($abbr);
        }
        return null;
    }

    /**
     * @param string $abbr
     * @param Ifw_Wp_Pathinfo_Plugin $pathinfo
     * @internal param array $plugin_path_info
     */
    protected function __construct($abbr, Ifw_Wp_Pathinfo_Plugin $pathinfo)
    {
        // set the plugin abbreviation
        $this->_abbr = $abbr;
        // set the pathinfo object
        $this->_pathinfo = $pathinfo;
    }
    
    /**
     * Bootstraps the plugin
     */
    public function bootstrap()
    {
        // create the plugin bootstrap object
        $this->_bootstrap = Ifw_Wp_Plugin_Bootstrap_Abstract::factory($this);
        $this->_bootstrap->init();

        // trigger event before_bootstrap
        Ifw_Wp_Proxy_Action::doAction($this->getAbbrLower() . '_before_bootstrap', $this->_bootstrap);

        $this->_bootstrap->bootstrap();

        // trigger event after_bootstrap
        Ifw_Wp_Proxy_Action::doAction($this->getAbbrLower() . '_after_bootstrap', $this->_bootstrap);
    }
    
    /**
     * Retrieves the plugin abbreviation
     */
    public function getAbbr()
    {
        return $this->_abbr;
    }
    
    /**
     * Retrieves the plugin abbreviation in lower case
     */
    public function getAbbrLower()
    {
        return strtolower($this->_abbr);
    }
    
    /**
     * @return Ifw_Wp_Pathinfo_Plugin $_pluginPathinfo
     */
    public function getPathinfo()
    {
        return $this->_pathinfo;
    }
    
    /**
     * @return Ifw_Wp_Plugin_Bootstrap_Abstract
     */
    public function getBootstrap()
    {
        return $this->_bootstrap;
    }

    /**
     * @return IfwZend_Controller_Front|null
     */
    public function getAdminFrontController()
    {
        $admin = $this->getBootstrap()->getAdmin();
        if ($admin instanceof Ifw_Wp_Plugin_Admin) {
            return $admin->getMenu()->getApplication()->getBootstrap()->getResource('FrontController');
        }
        return null;
    }

    /**
     * Determines if the admin pages of this plugin are accessed
     * @return bool
     */
    public function isExactAdminAccess()
    {
        // access to menu page or ajax request to exact plugin admin
        if ((isset($_GET['page']) && strpos($_GET['page'], $this->getPathinfo()->getDirname()) !== false) ||
            (Ifw_Wp_Ajax_Manager::isAccess() && isset($_REQUEST['action']) &&
                strpos($_REQUEST['action'], 'load-'. $this->getAbbrLower()) === 0)) {
            return true;
        }
        return false;
    }

    /**
     * Determines if the WP admin backend is accessed, no matter which page/plugin
     * @return bool
     */
    public function isGeneralAdminAccess()
    {
        return is_admin(); // && !Ifw_Wp_Ajax_Manager::isAccess();
    }
    
    /**
     * 
     * @return Ifw_Wp_Plugin_Config
     */
    public function getConfig()
    {
        return Ifw_Wp_Plugin_Config::getInstance($this->_pathinfo);
    }
    
    /**
     * Retrieves the plugin environment
     * 
     * @return Ifw_Wp_Env_Plugin
     */
    public function getEnv()
    {
        return Ifw_Wp_Env_Plugin::getInstance($this->_pathinfo);
    }

    /**
     * Retrieves the plugin logger
     *
     * @param string|null $name
     * @return Ifw_Wp_Plugin_Logger
     */
    public function getLogger($name = null)
    {
        return Ifw_Wp_Plugin_Logger::getInstance($this, $name);
    }
    
    /**
     * @return \Ifw_Wp_Options
     */
    public function getOptions()
    {
        return $this->getBootstrap()->getOptions();
    }

    /**
     * @return \Ifw_Wp_Options_Manager
     */
    public function getOptionsManager()
    {
        return $this->getBootstrap()->getOptionsManager();
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasOption($id)
    {
        return $this->getBootstrap()->getOptions()->hasOption($id);
    }

    /**
     * @param $id
     * @return null
     */
    public function getOption($id)
    {
        return $this->getBootstrap()->getOptions()->getOption($id);
    }
    
    /**
     * 
     * @param Exception $e
     */
    public function handleException(Exception $e)
    {
        echo 'Error: ' . $e->getMessage();
    }

    /**
     * Creates the plugin abbreviation from param $source
     *
     * Used for namespacing custom lib dir classes, ZendFW application ...
     *
     * @param source $source
     * @param bool|int $length
     * @return string Plugin abbreviation upper case first
     */
    protected static function _createAbbr($source, $length=false)
    {
        if ($length === false) {
            $length = self::$_defaultAbbrLength;
        }
        
        $delimiter = '-';
        if (strstr($source, '_')) {
            $delimiter = '_';
        }
        $name_parts = explode($delimiter, $source);
        $name_parts = array_slice($name_parts, 0, $length);
        
        $abbr = implode('', 
            array_map('substr', 
                $name_parts,
                array_fill(0, $length, 0), 
                array_fill(0, $length, 1)));
        
        return ucfirst($abbr);
    }

    /**
     * @return bool
     */
    public function hasPremium()
    {
        return $this->getConfig()->plugin->hasPremiumVersion == true;
    }

    /**
     * @return bool
     */
    public function isPremium()
    {
        return Ifw_Wp_Proxy_Filter::apply($this->getAbbrLower() . '_is_premium', false);
    }
}
