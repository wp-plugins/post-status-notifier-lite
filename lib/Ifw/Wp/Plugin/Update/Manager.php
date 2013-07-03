<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Handles update questions
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
class Ifw_Wp_Plugin_Update_Manager
{
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;



    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public function __construct(Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
    }

    public function init()
    {
        if ($this->_pm->isPremium()) {
            Ifw_Wp_Proxy_Action::add('after_plugin_row_' . $this->_pm->getPathinfo()->getFilenamePath(), array($this, 'onAfterPluginRow'), 10, 3);
        }
    }

    /**
     * @param $plugin_file
     * @param $plugin_data
     * @param $status
     */
    public function onAfterPluginRow($plugin_file, $plugin_data, $status)
    {
        if ($this->isUpdateAvailable()) {
            Ifw_Wp_Tpl::getFilesytemInstance($this->_pm)->display('update_row.twig', array(
                'name' => $this->_pm->getEnv()->getName(),
                'update_url' => Ifw_Wp_Plugin_RemoteInfo::getInstance($this->_pm->getConfig()->plugin->uniqueId)->getUpdateUrl(),
                'remote_version' => Ifw_Wp_Plugin_RemoteInfo::getInstance($this->_pm->getConfig()->plugin->uniqueId)->getVersion()
            ));
        }
    }

    /**
     * @return bool
     */
    public function isUpdateAvailable()
    {
        $installedVersion = $this->_pm->getEnv()->getVersion();
        $remoteVersion = Ifw_Wp_Plugin_RemoteInfo::getInstance($this->_pm->getConfig()->plugin->uniqueId)->getVersion();

        return version_compare($installedVersion, $remoteVersion) === -1;
    }
}
