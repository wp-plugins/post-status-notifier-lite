<?php
/**
 * Plugin bootstrap
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) ifeelweb.de
 * @version   $Id$
 */
class Psn_Bootstrap extends Ifw_Wp_Plugin_Bootstrap_Abstract
{
    /**
     * @var Psn_Notification_Manager
     */
    protected $_notificationManager;

    /**
     * Attach bootstrap observers
     */
    protected function _attachObservers()
    {
        $this->addObserver(new Psn_Bootstrap_Observer_MenuPage());
    }

    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Bootstrap_Abstract::bootstrap()
     */
    public function bootstrap()
    {
        if ($this->_pm->getAccess()->isAdmin()) {
            // on admin access

            // add plugin menu links
            Ifw_Wp_Proxy_Filter::addPluginActionLinks($this->_pm, array($this, 'addPluginActionLinks'));

            // set installer / uninstaller
            $this->getInstaller()->addActivation(new Psn_Installer_Activation());
            $this->getInstaller()->addUninstall(new Psn_Installer_Uninstall());
        }

        if ($this->_pm->getAccess()->isPlugin()) {

            // on PSN admin access
            $this->addOptions();

            // register patches
            $this->getUpdateManager()->getPatcher()->addPatch(new Psn_Patch_Database());
            // register selftests
            Ifw_Wp_Proxy_Action::addPlugin($this->_pm, 'selftester_activate', array($this, 'addSelftests'));
        }

        $this->_notificationManager = new Psn_Notification_Manager($this->_pm);
    }

    /**
     * @param Ifw_Wp_Plugin_Selftester $selftester
     */
    public function addSelftests(Ifw_Wp_Plugin_Selftester $selftester)
    {
        $selftester->addTestCase(new Psn_Test_RuleModel());
        $selftester->addTestCase(new Psn_Test_BccField());
        $selftester->addTestCase(new Psn_Test_CategoriesField());
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
        $this->getOptionsManager()->addGeneralOption(new Ifw_Wp_Options_Field_Checkbox(
            'psn_hide_nonpublic_posttypes',
            __('Hide non-public post types', 'psn'),
            __('When selected, non-public post types will be excluded from rule settings form', 'psn')
        ));

        if (!$this->_pm->isPremium()) {
            $smtpOptions = new Ifw_Wp_Options_Section('smtp', __('SMTP', 'psn_smtp'));
            $smtpOptions->addField(new Ifw_Wp_Options_Field_Checkbox(
                'smtp_teaser',
                __('Activate SMTP', 'psn'),
                __('SMTP is a premium feature. You will get all configuration options to connect to your SMTP server.', 'psn')
            ));
            $this->_pm->getBootstrap()->getOptions()->addSection($smtpOptions, 12);
        }

        $placeholderFilterOptions = new Ifw_Wp_Options_Section('placeholders', __('Placeholders', 'psn'));

        $placeholderFilterOptions->addField(new Ifw_Wp_Options_Field_Textarea(
            'placeholders_filters',
            __('Placeholders filters', 'psn'),
            sprintf( __('Here you can define filters which will apply to the placeholders contents (One filter per line). You can use the <a href="%s" target="_blank">Twig filters</a>. Refer to the <a href="%s" target="_blank">documentation</a> for details.<br>Example: [post_date]|date("m/d/Y")', 'psn_smtp'),
                'http://twig.sensiolabs.org/doc/filters/index.html',
                'http://docs.ifeelweb.de/post-status-notifier/options.html#placeholders')
        ));

        $this->_pm->getBootstrap()->getOptions()->addSection($placeholderFilterOptions, 11);
    }

    /**
     * 
     */
    public function addPluginActionLinks($links, $file)
    {
        $links[] = '<a href="' . substr(Ifw_Wp_Proxy_Admin::getMenuUrl($this->_pm, 'index'), 1) . '">' . __('Settings', 'psn') . '</a>';
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
