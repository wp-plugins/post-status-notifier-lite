<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 *
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
class Ifw_Wp_Env_Plugin extends Ifw_Wp_Env_Abstract
{
    /**
     * @var string
     */
    protected $_urlAdminCss;

    /**
     * @var string
     */
    protected $_urlAdminJs;

    /**
     * @var string
     */
    protected $_environment;

    /**
     * @var string
     */
    protected $_skinRoot;

    /**
     * @var string
     */
    protected $_skinUrl;

    /**
     * @var string
     */
    protected $_phpSapiName;

    /**
     * Operation system
     * @var string
     */
    protected $_os;

    /**
     * Instance store
     * @var array
     */
    public static $_instances = array();



    /**
     * Retrieves singleton Ifw_Wp_Plugin_Config object
     *
     * @param Ifw_Wp_Pathinfo_Plugin $pathinfo
     * @return Ifw_Wp_Plugin_Config
     */
    public static function getInstance(Ifw_Wp_Pathinfo_Plugin $pathinfo)
    {
        $instanceToken = $pathinfo->getFilename();

        if (!isset(self::$_instances[$instanceToken])) {
            self::$_instances[$instanceToken] = new self($pathinfo);
        }
        return self::$_instances[$instanceToken];
    }

    /**
     * @return string the $_name
     */
    public function getName()
    {
        return Ifw_Wp_Proxy_Filter::apply($this->_pathinfo->getDirname() . '_plugin_name', $this->_name);
    }

    /**
     *
     */
    protected function _init()
    {
        $this->_phpSapiName = php_sapi_name();
        $this->_os = PHP_OS;

        if (!$this->isCli() && function_exists('plugins_url')) {
            $this->_url = plugins_url($this->_pathinfo->getDirname()) . '/';
            $this->_urlFiles = $this->_url . 'files/';
            $this->_urlCss = $this->_urlFiles . 'css/';
            $this->_urlJs = $this->_urlFiles . 'js/';
            $this->_urlImg = $this->_urlFiles . 'img/';
            $this->_urlAdminCss = $this->_url . 'admin/css/';
            $this->_urlAdminJs = $this->_url . 'admin/js/';
        }

        $this->_environment = getenv('IFW_WP_ENV') ? getenv('IFW_WP_ENV') : 'production';

        $this->_parsePluginHeaderComment();

        $this->_initSkin();
    }

    /**
     * @return string string
     */
    public function getEnvironmet()
    {
        return $this->_environment;
    }

    /**
     *
     */
    protected function _initSkin()
    {
        $skin_root = $this->_pathinfo->getRootSkin() . 'default';
        if (is_dir($skin_root)) {
            $this->_skinRoot = $skin_root;
            $this->_skinUrl = $this->_url . 'skin/default/';
        }
    }

    /**
     *
     * @return string
     */
    public function getSkinRoot()
    {
        return $this->_skinRoot;
    }

    /**
     *
     * @return string
     */
    public function getSkinUrl()
    {
        return $this->_skinUrl;
    }

    /**
     * @return bool
     */
    public function hasSkin()
    {
        return is_dir($this->_pathinfo->getRootSkin());
    }

    /**
     * @return the $_urlAdminCss
     */
    public function getUrlAdminCss ()
    {
        return $this->_urlAdminCss;
    }

    /**
     * @return the $_urlAdminJs
     */
    public function getUrlAdminJs ()
    {
        return $this->_urlAdminJs;
    }

    /**
     * Check if script is called via command line interface
     * @return boolean
     */
    public function isCli()
    {
        return $this->_phpSapiName == 'cli';
    }

    /**
     * Checks if os is windows
     * @return boolean
     */
    public function isWindows()
    {
        return stristr($this->_os, 'windows') != false;
    }

    /**
     * Checks if os is linux
     * @return boolean
     */
    public function isLinux()
    {
        return stristr($this->_os, 'linux') != false;
    }

    /**
     * Checks if os is mac
     * @return boolean
     */
    public function isMac()
    {
        return stristr($this->_os, 'darwin') != false;
    }

    /**
     * Parses the plugin header comment and set properties
     *
     * Plugin URI
     */
    protected function _parsePluginHeaderComment()
    {
        $pluginRootFile = file_get_contents($this->_pathinfo->getRoot() . $this->_pathinfo->getBasename());

        $vars = array(
            '_name' => 'Plugin Name:(.*)',
            '_description' => 'Description:(.*)',
            '_textDomain' => 'Text Domain:(.*)',
            '_homepage' => 'Plugin URI:(.*)',
            '_version' => 'Version:(.*)',
        );

        foreach ($vars as $k => $v) {
            preg_match('/'. $v .'/', $pluginRootFile, $match);
            if (is_array($match) && !empty($match[1])) {
                $this->$k = trim($match[1]);
            }
        }
    }
}
