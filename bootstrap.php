<?php
/**
 * Plugin bootstrap
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2012-2013 ifeelweb.des
 * @version   $Id$
 */
class Psn_Bootstrap extends Ifw_Wp_Plugin_Bootstrap_Abstract
{
    /**
     * @var Psn_Notification_Manager
     */
    protected $_notificationManager;



    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Bootstrap_Abstract::bootstrap()
     */
    public function bootstrap()
    {
        if ($this->_pm->isGeneralAdminAccess()) {
            // add options page
            $this->getAdmin()->getMenu()->addOptionsPage($this->_pm->getEnv()->getName(), false);
            // add plugin menu links
            $this->getAdmin()->addPluginMenuActionLinks(array($this, 'addPluginActionLinks'));

            // set installer / uninstaller
            $this->getInstaller()->addActivation(new Psn_Installer_Activation());
            $this->getInstaller()->addUninstall(new Psn_Installer_Uninstall());

            $this->getSelftester()->addTestCase(new Psn_Test_RuleModel());
        }

        $this->addOptions();

        $this->_notificationManager = new Psn_Notification_Manager($this->_pm);
    }

    /**
     * @internal param \Ifw_Wp_Options_Section $generalOptions
     */
    public function addOptions()
    {
        $this->getOptionsManager()->addGeneralOption(new Ifw_Wp_Options_Field_Checkbox(
            'psn_ignore_status_inherit',
            __('Ignore post status "inherit"', 'psn'),
            __('Status "inherit" is used when post revisions get created by WordPress automatically', 'psn')
        ));

    }

    /**
     * 
     */
    public function addPluginActionLinks($links, $file)
    {
        $links[] = '<a href="' . $this->getAdmin()->getMenu()->getOptionsPagePath() . '">' . __('Settings', 'psn') . '</a>';
        return $links;
    }

    /**
     * @return \Psn_Notification_Manager
     */
    public function getNotificationManager()
    {
        return $this->_notificationManager;
    }

}
