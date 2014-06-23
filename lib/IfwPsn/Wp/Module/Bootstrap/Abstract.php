<?php
/**
 * ifeelweb.de Wordpress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Abstract module
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  IfwPsn_Wp_Plugin_Admin
 */
require_once dirname(__FILE__) . '/Interface.php';

abstract class IfwPsn_Wp_Module_Bootstrap_Abstract implements IfwPsn_Wp_Module_Bootstrap_Interface
{
    /**
     * @var IfwPsn_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var IfwPsn_Wp_Env_Module
     */
    protected $_env;

    /**
     * @var IfwPsn_Wp_Pathinfo_Module
     */
    protected $_pathinfo;

    /**
     * @var bool
     */
    protected $_initialized = false;


    /**
     * @param $bootstrapPath
     * @param IfwPsn_Wp_Plugin_Manager $pm
     */
    public final function __construct($bootstrapPath, IfwPsn_Wp_Plugin_Manager $pm)
    {
        require_once $pm->getPathinfo()->getRootLib() . '/IfwPsn/Wp/Pathinfo/Module.php';
        require_once $pm->getPathinfo()->getRootLib() . '/IfwPsn/Wp/Env/Module.php';

        $this->_pathinfo = new IfwPsn_Wp_Pathinfo_Module($bootstrapPath);
        // init module environment
        $this->_env = IfwPsn_Wp_Env_Module::getInstance($this->_pathinfo);

        $this->_pm = $pm;
    }

    /**
     * Inits default logic
     */
    public function init()
    {
        // check if module properties are set
        $this->_checkProperties();

        // register lib
        if ($this->_pathinfo->hasRootLib()) {
            // add the module lib dir to the autoloader
            $classPrefix = $this->_pm->getAbbr() . '_Module_' . $this->_pathinfo->getDirname();
            IfwPsn_Wp_Autoloader::registerModule($classPrefix, $this->_pathinfo->getRootLib());
        }

        // register templates dir
        if ($this->_pm->getAccess()->isPlugin()) {
            $this->initTpl();
        }

        // init module translation
        $this->_initTranslation();

        // load default script and style files
        if ($this->_pm->getAccess()->isPlugin() && !$this->_pm->getAccess()->isAjax()) {
            $this->_enqueueScripts();
        }

        if ($this->_pm->getAccess()->isAjax() && !$this->_pm->getAccess()->isHeartbeat()) {
            $this->_initAjax();
        }

        $this->_initialized = true;
    }

    /**
     * @return bool
     * @throws IfwPsn_Wp_Module_Exception
     */
    protected function _checkProperties()
    {
        $properties = array('_id', '_name', '_description', '_textDomain', '_version', '_author', '_authorMail', '_homepage', '_dependencies');
        foreach ($properties as $prop) {
            if (!isset($this->$prop)) {
                throw new IfwPsn_Wp_Module_Exception('Module must have $' . $prop);
            }
        }
        return true;
    }

    /**
     * Registers the controller path
     */
    public function registerPath()
    {
        if ($this->_pm->getAccess()->isPlugin()) {

            // add controller dir
            if(is_dir($this->_pathinfo->getDirnamePath() . 'controllers')) {

                $front = IfwPsn_Zend_Controller_Front::getInstance();
                if ($front instanceof IfwPsn_Vendor_Zend_Controller_Front) {
                    $front->addControllerDirectory($this->_pathinfo->getDirnamePath() . 'controllers',
                        strtolower($this->_pathinfo->getDirname()));
                }
            }
        }
    }

    /**
     * register the module's tpl dir to loader path
     */
    public function initTpl()
    {
        if ($this->_pathinfo->hasRootTpl()) {
            IfwPsn_Wp_Tpl::getFilesytemInstance($this->_pm)->getLoader()->addPath($this->_pathinfo->getRootTpl());
        }
    }

    /**
     * registeres the module's files that handle the ajax requests
     */
    protected function _initAjax()
    {
        if (method_exists($this, '_registerAjaxRequests')) {
            $requests = $this->_registerAjaxRequests();
            if (!is_array($requests)) {
                $requests = array($requests);
            }
            foreach ($requests as $request) {
                $this->_pm->getAjaxManager()->registerRequest($request);
            }
        }
    }

    /**
     * Checks if plugin translation files exist and inits the plugin textdomain
     *
     * @return bool
     */
    protected function _initTranslation()
    {
        $result = false;

        if ($this->_pathinfo->hasRootLang()) {
            $langRelPath = $this->_pm->getPathinfo()->getDirname() . '/modules/' . $this->_pathinfo->getDirname() . '/lang';
            $result = IfwPsn_Wp_Proxy::loadTextdomain($this->getTextDomain(), false, $langRelPath);
        }

        return $result;
    }

    /**
     * Loads js/css on admin_enqueue_scripts
     */
    protected function _enqueueScripts()
    {
        if ($this->_pm->getAccess()->isModule(strtolower($this->getName()))) {
            $this->_loadAdminCss();
            $this->_loadAdminJs();
        }
    }

    /**
     *
     */
    protected function _loadAdminCss()
    {
        $adminCssPath = $this->_pathinfo->getRootCss() . 'admin.css';

        if (file_exists($adminCssPath)) {
            $handle = $this->getId() . '-' .'admin-css';
            IfwPsn_Wp_Proxy_Style::loadAdmin($handle, $this->_env->getUrlCss() . 'admin.css');
        }
    }

    /**
     *
     */
    protected function _loadAdminJs()
    {
        $adminJsPath = $this->_pathinfo->getRootJs() . 'admin.js';

        if (file_exists($adminJsPath)) {
            $handle = $this->getId() . '-' .'admin-js';
            IfwPsn_Wp_Proxy_Script::loadAdmin($handle, $this->_env->getUrlJs() . 'admin.js');
        }
    }

    /**
     * @return \IfwPsn_Wp_Env_Module
     */
    public function getEnv()
    {
        return $this->_env;
    }

    /**
     * @return \IfwPsn_Wp_Pathinfo_Module
     */
    public function getPathinfo()
    {
        return $this->_pathinfo;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->_author;
    }

    /**
     * @return string
     */
    public function getAuthorMail()
    {
        return $this->_authorMail;
    }

    /**
     * @return string
     */
    public function getDependencies()
    {
        if (!is_array($this->_dependencies)) {
            $this->_dependencies = array($this->_dependencies);
        }
        return $this->_dependencies;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @return string
     */
    public function getHomepage()
    {
        return $this->_homepage;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getTextDomain()
    {
        return $this->_textDomain;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * @return bool
     */
    public function isInitialized()
    {
        return $this->_initialized === true;
    }

}
