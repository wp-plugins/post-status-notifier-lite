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
require_once dirname(__FILE__) . '/Abstract.php';
require_once dirname(__FILE__) . '/Exception.php';

class IfwPsn_Wp_Env_Module extends IfwPsn_Wp_Env_Abstract
{
    /**
     * @var IfwPsn_Wp_Pathinfo_Module
     */
    protected $_pathinfo;

    /**
     * @var string
     */
    protected $_id;

    /**
     * @var array
     */
    protected $_dependencies = array();

    /**
     * Instance store
     * @var array
     */
    public static $_instances = array();



    /**
     * Retrieves singleton IfwPsn_Wp_Plugin_Config object
     *
     * @param IfwPsn_Wp_Pathinfo_Module $pathinfo
     * @return IfwPsn_Wp_Plugin_Config
     */
    public static function getInstance(IfwPsn_Wp_Pathinfo_Module $pathinfo)
    {
        $instanceToken = $pathinfo->getDirname();

        if (!isset(self::$_instances[$instanceToken])) {
            self::$_instances[$instanceToken] = new self($pathinfo);
        }
        return self::$_instances[$instanceToken];
    }

    /**
     * @return mixed
     */
    protected function _init()
    {
        $dirnamePathParts = array_reverse(explode(DIRECTORY_SEPARATOR, $this->_pathinfo->getDirnamePath()));

        $this->_url = plugins_url($dirnamePathParts[3]) . '/modules/' . $this->_pathinfo->getDirname() . '/';
        $this->_urlFiles = $this->_url . 'files/';
        $this->_urlCss = $this->_urlFiles . 'css/';
        $this->_urlJs = $this->_urlFiles . 'js/';
        $this->_urlImg = $this->_urlFiles . 'img/';
    }

    /**
     * Retrieves the module's id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return $this->_dependencies;
    }

}
