<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Reads remote info about the plugin like current version and URL to changelog / update
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) ifeelweb.de
 * @version   $Id$
 * @package   Ifw_Wp_Plugin
 */
class Ifw_Wp_Plugin_RemoteInfo
{
    /**
     * Instance store
     * @var array
     */
    public static $_instances = array();
    
    /**
     * @var string
     */
    protected $_remoteInfoUrl = 'http://www.ifeelweb.de/plugins.xml';
    
    /**
     * @var string
    */
    protected $_uniqueId;
    
    /**
     * @var string
    */
    protected $_name;
    
    /**
     * @var string
    */
    protected $_version;
    
    /**
     * @var string
    */
    protected $_changelogUrl;
    
    /**
     * @var string
    */
    protected $_updateUrl;
    
    /**
     * @var string
    */
    protected $_homepageUrl;
    
    
    
    /**
     * Retrieves singleton Ifw_Wp_Plugin_Admin object
     *
     * @param string 
     * @return Ifw_Wp_Plugin_RemoteInfo
     */
    public static function getInstance($pluginUniqueId)
    {
        if (!isset(self::$_instances[$pluginUniqueId])) {
            self::$_instances[$pluginUniqueId] = new self($pluginUniqueId);
        }
        return self::$_instances[$pluginUniqueId];
    }
    
    /**
     * 
     * @param string $pluginUniqueId
     */
    protected function __construct($pluginUniqueId)
    {
        $this->_uniqueId = $pluginUniqueId;
        $this->_loadRemoteInfo();
    }
    
    /**
     * 
     */
    public function load()
    {
        $this->_loadRemoteInfo();
    }
    
    /**
     *
     * @throws Ifw_Wp_Plugin_Metabox_Exception
     * @return unknown
     */
    protected function _loadRemoteInfo()
    {
        $xml = simplexml_load_string($this->_getRemoteInfoXml());
        if ($xml === false) {
            throw new Ifw_Wp_Plugin_Metabox_Exception('Remote plugin info is not valid XML.');
        }
    
        foreach ($xml->plugin as $plugin) {
            if ((string)$plugin->uniqueId === $this->_uniqueId) {
                $this->_initProperties($plugin);
                return;
            }
        }
        
        throw new Ifw_Wp_Plugin_Metabox_Exception('Remote plugin info was not found.');
    }
    
    /**
     * 
     * @param unknown_type $xml
     */
    protected function _initProperties($xml)
    {
        $this->_name = (string)$xml->name;
        $this->_version = (string)$xml->version;
        $this->_changelogUrl = (string)$xml->changelogUrl;
        $this->_updateUrl = (string)$xml->updateUrl;
        $this->_homepageUrl = (string)$xml->homepageUrl;
    }

    /**
     *
     * @throws Ifw_Wp_Plugin_Metabox_Exception
     * @return unknown
     */
    protected function _getRemoteInfoXml()
    {
        $client = new IfwZend_Http_Client($this->_remoteInfoUrl);
        $response = $client->request('GET');
    
        if ($response->isSuccessful()) {
            return $response->getBody();
        }
    
        throw new Ifw_Wp_Plugin_Metabox_Exception('Could not retrieve remote XML from ' . $this->_remoteInfoUrl);
    }
    
    /**
     * @return the $_uniqueId
     */
    public function getUniqueId ()
    {
        return $this->_uniqueId;
    }

    /**
     * @return the $_name
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * @return the $_version
     */
    public function getVersion ()
    {
        return $this->_version;
    }

    /**
     * @return the $_changelogUrl
     */
    public function getChangelogUrl ()
    {
        return $this->_changelogUrl;
    }

    /**
     * @return the $_updateUrl
     */
    public function getUpdateUrl ()
    {
        return $this->_updateUrl;
    }

    /**
     * @return the $_homepageUrl
     */
    public function getHomepageUrl ()
    {
        return $this->_homepageUrl;
    }

}