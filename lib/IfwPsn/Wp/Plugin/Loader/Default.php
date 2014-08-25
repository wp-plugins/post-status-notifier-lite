<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Default loader
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  IfwPsn_Wp
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Wp/Plugin/Loader/Abstract.php';

class IfwPsn_Wp_Plugin_Loader_Default extends IfwPsn_Wp_Plugin_Loader_Abstract
{
    /**
     * @var IfwPsn_Wp_Plugin_Config
     */
    protected $_config;

    /**
     * @var IfwPsn_Wp_Env_Abstract
     */
    protected $_env;

    /**
     * @var IfwPsn_Wp_Plugin_Manager
     */
    protected $_pm;




    /**
     * (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Loader_Abstract::load()
     */
    public function load()
    {
//        $this->loadIncludePath();
        $this->loadConfig();
        $this->loadEnv();
        $this->loadORM();
        $this->loadPluginManager();
    }

//    public function loadIncludePath()
//    {
//        $result = set_include_path(get_include_path() . PATH_SEPARATOR . $this->_pluginPathinfo->getRootLib());
//        if (!$result) {
//            trigger_error('
//                Post Status Notifier could not extend the <b>PHP include_path</b> on this server, which is required for this plugin to work.
//                This is a <b>known issue</b> and no bug. Probably the PHP include_path was set with php_admin_value.<br>
//                The include_path is fixed to "'. get_include_path() .'"<br>
//                Please contact your server support.<br> Exiting activation.<br><br>', E_USER_ERROR);
//        }
//    }

    public function loadConfig()
    {
        require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Wp/Plugin/Config.php';
        $this->_config = IfwPsn_Wp_Plugin_Config::getInstance($this->_pluginPathinfo);
    }

    public function loadEnv()
    {
        require_once IFW_PSN_LIB_ROOT . '/IfwPsn/Wp/Env/Plugin.php';
        $this->_env = IfwPsn_Wp_Env_Plugin::getInstance($this->_pluginPathinfo);
    }

    public function loadORM()
    {
        if (isset($this->_config->orm->init) && $this->_config->orm->init == true) {

            require_once IFW_PSN_LIB_ROOT . '/IfwPsn/Wp/ORM.php';
            IfwPsn_Wp_ORM::init($this->_config->orm);
        }
    }

    public function loadPluginManager()
    {
        require_once IFW_PSN_LIB_ROOT . '/IfwPsn/Wp/Plugin/Manager.php';
        $this->_pm = IfwPsn_Wp_Plugin_Manager::init($this->_pluginPathinfo);
        $this->_pm->getLogger()->log($this->_pm->getAbbr() . ': Pluginmanager loaded.');
    }

    /**
     * @return IfwPsn_Wp_Plugin_Logger
     */
    public function getLogger()
    {
        return $this->_pm->getLogger();
    }

    /**
     * @return IfwPsn_Wp_Plugin_Manager
     */
    public function getPluginManager()
    {
        return $this->_pm;
    }

    /**
     * @return IfwPsn_Wp_Env_Plugin
     */
    public function getEnv()
    {
        return $this->_env;
    }

    /**
     * 
     */
    protected function _debugPathinfo()
    {
        // check for pathinfo logging
        if ($this->_config->debug->pathinfo) {
            $this->_pluginPathinfo->setLogger($this->_pm->getLogger());
            $this->_pluginPathinfo->__toString();
        }
    }
}