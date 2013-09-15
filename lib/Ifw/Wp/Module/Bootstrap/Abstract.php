<?php
/**
 * ifeelweb.de Wordpress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Abstract module
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp_Plugin_Admin
 */
abstract class Ifw_Wp_Module_Bootstrap_Abstract implements Ifw_Wp_Module_Bootstrap_Interface
{
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var Ifw_Wp_Env_Module
     */
    protected $_env;

    /**
     * @var Ifw_Wp_Pathinfo_Module
     */
    protected $_pathinfo;


    /**
     * @param $bootstrapPath
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public function __construct($bootstrapPath, Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pathinfo = new Ifw_Wp_Pathinfo_Module($bootstrapPath);
        // init module environment
        $this->_env = Ifw_Wp_Env_Module::getInstance($this->_pathinfo);
        $this->_pm = $pm;
    }

    /**
     * Inits default logic
     */
    public function init()
    {
        $this->_init();

        if (Ifw_Wp_Ajax_Manager::isAccess()) {
            $this->_initAjax();
        }
    }

    /**
     * Initializes the module
     */
    protected function _init()
    {
        // add the module lib dir to the autoloader
        $classPrefix = $this->_pm->getAbbr() . '_Module_' . $this->_pathinfo->getDirname();
        Ifw_Wp_Autoloader::registerModule($classPrefix, $this->_pathinfo->getRootLib());

        if ($this->_pm->getAccess()->isPlugin()) {
            $this->initTpl();
        }

        // init module translation
        $this->_initTranslation();

        $this->_enqueueScripts();
    }

    /**
     * Registers the controller path
     */
    public function registerPath()
    {
        if ($this->_pm->getAccess()->isPlugin()) {

            // add controller dir
            if(is_dir($this->_pathinfo->getDirnamePath() . 'controllers')) {

                $front = Ifw_Zend_Controller_Front::getInstance();
                if ($front instanceof IfwZend_Controller_Front) {
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
        if (is_dir($this->_pathinfo->getRootTpl())) {
            Ifw_Wp_Tpl::getFilesytemInstance($this->_pm)->getLoader()->addPath($this->_pathinfo->getRootTpl());
        }
    }

    /**
     * if the request accesses the wp admin-ajax, load the ajax manager
     */
    protected function _initAjax()
    {
        $ajaxManager = Ifw_Wp_Ajax_Manager::getInstance($this->_pathinfo->getRoot());
        $ajaxManager->load($this->_pm);
    }

    /**
     * Checks if plugin translation files exist and inits the plugin textdomain
     *
     * @return bool
     */
    protected function _initTranslation()
    {
        $result = false;

        if (is_dir($this->_pathinfo->getRootLang())) {
            $langRelPath = $this->_pm->getPathinfo()->getDirname() . '/modules/' . $this->_pathinfo->getDirname() . '/lang';
            $result = Ifw_Wp_Proxy::loadTextdomain($this->_env->getTextDomain(), false, $langRelPath);
        }

        return $result;
    }

    /**
     * Loads js/css on admin_enqueue_scripts
     */
    protected function _enqueueScripts()
    {
        $this->_loadAdminCss();
        $this->_loadAdminJs();
    }

    /**
     *
     */
    protected function _loadAdminCss()
    {
        $adminCssPath = $this->_pathinfo->getRootCss() . 'admin.css';

        if ($this->_pm->getAccess()->isPlugin() && !$this->_pm->getAccess()->isAjax() && file_exists($adminCssPath)) {
            $handle = $this->_env->getId() . '-' .'admin-css';
            Ifw_Wp_Proxy_Style::loadAdmin($handle, $this->_env->getUrlCss() . 'admin.css');
        }
    }

    /**
     *
     */
    protected function _loadAdminJs()
    {
        $adminJsPath = $this->_pathinfo->getRootJs() . 'admin.js';

        if ($this->_pm->getAccess()->isPlugin() && !$this->_pm->getAccess()->isAjax() && file_exists($adminJsPath)) {
            $handle = $this->_env->getId() . '-' .'admin-js';
            Ifw_Wp_Proxy_Script::loadAdmin($handle, $this->_env->getUrlJs() . 'admin.js');
        }
    }

    /**
     * @return \Ifw_Wp_Env_Module
     */
    public function getEnv()
    {
        return $this->_env;
    }

    /**
     * @return \Ifw_Wp_Pathinfo_Module
     */
    public function getPathinfo()
    {
        return $this->_pathinfo;
    }

}
