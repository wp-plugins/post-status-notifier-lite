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
class Ifw_Wp_Env_Module extends Ifw_Wp_Env_Abstract
{
    /**
     * @var Ifw_Wp_Pathinfo_Module
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
     * Retrieves singleton Ifw_Wp_Plugin_Config object
     *
     * @param Ifw_Wp_Pathinfo_Module $pathinfo
     * @return Ifw_Wp_Plugin_Config
     */
    public static function getInstance(Ifw_Wp_Pathinfo_Module $pathinfo)
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
        $this->_getModuleMetaData();

        $dirnamePathParts = array_reverse(explode(DIRECTORY_SEPARATOR, $this->_pathinfo->getDirnamePath()));

        $this->_url = plugins_url($dirnamePathParts[3]) . '/modules/' . $this->_pathinfo->getDirname() . '/';
        $this->_urlFiles = $this->_url . 'files/';
        $this->_urlCss = $this->_urlFiles . 'css/';
        $this->_urlJs = $this->_urlFiles . 'js/';
        $this->_urlImg = $this->_urlFiles . 'img/';
    }

    /**
     * Reads module data from module.xml
     *
     * @throws Ifw_Wp_Env_Exception
     */
    protected function _getModuleMetaData()
    {
        if (!file_exists($this->_pathinfo->getMetaDataPath())) {
            throw new Ifw_Wp_Env_Exception('Missing module.xml for module '. $this->_pathinfo->getDirname());
        }

        try {
            $meta = new SimpleXMLElement(file_get_contents($this->_pathinfo->getMetaDataPath()));
            $this->_id = (string)$meta->id;
            $this->_name = (string)$meta->name;
            $this->_description = (string)$meta->description;
            $this->_textDomain = (string)$meta->textDomain;
            $this->_version = (string)$meta->version;
            $this->_homepage = (string)$meta->homepage;
            $this->_dependencies = (string)$meta->dependencies != '' ? explode(',', (string)$meta->dependencies) : array();

        } catch (Exception $e) {
            throw new Ifw_Wp_Env_Exception('Could not load module.xml for module '. $this->_pathinfo->getDirname());
        }
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
