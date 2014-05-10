<?php
/**
 * ifeelweb.de Wordpress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Plugin Manager
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) ifeelweb.de
 * @version   $Id$
 * @package   IfwPsn_Wp_Plugin
 */
class IfwPsn_Wp_Plugin_Manager
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
     * @var IfwPsn_Wp_Pathinfo_Plugin
     */
    protected $_pathinfo;

    /**
     * @var IfwPsn_Wp_Access
     */
    protected $_access;
    
    /**
     * Abbreviation character length
     * @var int
     */
    protected static $_defaultAbbrLength = 3;

    /**
     * @var IfwPsn_Wp_Plugin_Config
     */
    protected $_config;

    /**
     * @var IfwPsn_Wp_Plugin_Bootstrap_Abstract
     */
    protected $_bootstrap;



    /**
     * Initializes the plugin manager
     *
     * @param IfwPsn_Wp_Pathinfo_Plugin $pluginPathinfo
     * @param bool|\false|string $abbr
     * @return IfwPsn_Wp_Plugin_Manager
     */
    public static function init(IfwPsn_Wp_Pathinfo_Plugin $pluginPathinfo, $abbr=false)
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
     * Retrieves singleton instance of IfwPsn_Wp_Plugin_Manager
     *
     * @param string
     * @throws IfwPsn_Wp_Plugin_Exception
     * @return IfwPsn_Wp_Plugin_Manager
     */
    public static function getInstance($abbr)
    {      
        if (!isset(self::$_instances[$abbr])) {
            throw new IfwPsn_Wp_Plugin_Exception('No instance stored under '. $abbr);
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
     * @return IfwPsn_Wp_Plugin_Manager|null
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
     * @param IfwPsn_Wp_Pathinfo_Plugin $pathinfo
     * @internal param array $plugin_path_info
     */
    protected function __construct($abbr, IfwPsn_Wp_Pathinfo_Plugin $pathinfo)
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
        require_once $this->getPathinfo()->getRootLib() . '/IfwPsn/Wp/Plugin/Bootstrap/Abstract.php';

        $this->_bootstrap = IfwPsn_Wp_Plugin_Bootstrap_Abstract::factory($this);

        $this->_bootstrap->run();
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
     * @return IfwPsn_Wp_Pathinfo_Plugin $_pluginPathinfo
     */
    public function getPathinfo()
    {
        return $this->_pathinfo;
    }
    
    /**
     * @return IfwPsn_Wp_Plugin_Bootstrap_Abstract
     */
    public function getBootstrap()
    {
        return $this->_bootstrap;
    }

    /**
     * @return IfwPsn_Wp_Access
     */
    public function getAccess()
    {
        if (empty($this->_access)) {
            require_once $this->getPathinfo()->getRootLib() . '/IfwPsn/Wp/Access.php';
            $this->_access = new IfwPsn_Wp_Access($this);
        }
        return $this->_access;
    }

    /**
     * @return IfwPsn_Vendor_Zend_Controller_Front|null
     */
    public function getAdminFrontController()
    {
        $admin = $this->getBootstrap()->getAdmin();
        if ($admin instanceof IfwPsn_Wp_Plugin_Admin) {
            return $admin->getMenu()->getApplication()->getBootstrap()->getResource('FrontController');
        }
        return null;
    }

    /**
     * 
     * @return IfwPsn_Wp_Plugin_Config
     */
    public function getConfig()
    {
        return IfwPsn_Wp_Plugin_Config::getInstance($this->_pathinfo);
    }
    
    /**
     * Retrieves the plugin environment
     * 
     * @return IfwPsn_Wp_Env_Plugin
     */
    public function getEnv()
    {
        return IfwPsn_Wp_Env_Plugin::getInstance($this->_pathinfo);
    }

    /**
     * Retrieves the plugin logger
     *
     * @param string|null $name
     * @return IfwPsn_Wp_Plugin_Logger
     */
    public function getLogger($name = null)
    {
        return IfwPsn_Wp_Plugin_Logger::getInstance($this, $name);
    }

    /**
     * @return IfwPsn_Wp_Ajax_Manager
     */
    public function getAjaxManager()
    {
        return IfwPsn_Wp_Ajax_Manager::getInstance($this);
    }
    
    /**
     * @return \IfwPsn_Wp_Options
     */
    public function getOptions()
    {
        return $this->getBootstrap()->getOptions();
    }

    /**
     * @return \IfwPsn_Wp_Options_Manager
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
        return IfwPsn_Wp_Proxy_Filter::apply($this->getAbbrLower() . '_is_premium', false);
    }
}
