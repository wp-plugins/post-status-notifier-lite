<?php
/**
 * Plugin bootstrap
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) ifeelweb.de
 * @version   $Id$
 */
class Psn_Bootstrap extends IfwPsn_Wp_Plugin_Bootstrap_Abstract
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
        require_once $this->_pm->getPathinfo()->getRootLib() . '/Psn/Bootstrap/Observer/MenuPage.php';
        $this->addObserver(new Psn_Bootstrap_Observer_MenuPage());
    }

    /**
     * @return array
     */
    public function registerAjaxRequests()
    {
        return array(
            dirname(__FILE__) . '/admin/ajax/load-psn-ifeelweb_de.php',
            dirname(__FILE__) . '/admin/ajax/load-psn-plugin_info.php',
            dirname(__FILE__) . '/admin/ajax/load-psn-plugin_status.php',
            dirname(__FILE__) . '/admin/ajax/load-psn-rules.php',
        );
    }

    /**
     * (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Bootstrap_Abstract::bootstrap()
     */
    public function bootstrap()
    {
        if ($this->_pm->getAccess()->isAdmin()) {
            // on admin access

            // add plugin menu links
            IfwPsn_Wp_Proxy_Filter::addPluginActionLinks($this->_pm, array($this, 'addPluginActionLinks'));

            // set installer / uninstaller
            require_once $this->_pm->getPathinfo()->getRootLib() . 'Psn/Installer/Activation.php';
            require_once $this->_pm->getPathinfo()->getRootLib() . 'Psn/Installer/Uninstall.php';

            $this->getInstaller()->addActivation(new Psn_Installer_Activation());
            $this->getInstaller()->addUninstall(new Psn_Installer_Uninstall());
        }

        if ($this->_pm->getAccess()->isPlugin()) {

            // on PSN admin access
            $this->addOptions();

            // register patches
            require_once $this->_pm->getPathinfo()->getRootLib() . 'Psn/Patch/Database.php';
            $this->getUpdateManager()->getPatcher()->addPatch(new Psn_Patch_Database());
            // register selftests
            IfwPsn_Wp_Proxy_Action::addPlugin($this->_pm, 'selftester_activate', array($this, 'addSelftests'));
        }

        require_once $this->_pm->getPathinfo()->getRootLib() . '/Psn/Notification/Manager.php';
        $this->_notificationManager = new Psn_Notification_Manager($this->_pm);
        $this->_notificationManager->setDeferredExecution();
    }

    /**
     * @param IfwPsn_Wp_Plugin_Selftester $selftester
     */
    public function addSelftests(IfwPsn_Wp_Plugin_Selftester $selftester)
    {
        require_once $this->_pm->getPathinfo()->getRootLib() . '/Psn/Test/RuleModel.php';
        require_once $this->_pm->getPathinfo()->getRootLib() . '/Psn/Test/BccField.php';
        require_once $this->_pm->getPathinfo()->getRootLib() . '/Psn/Test/BccSelectField.php';
        require_once $this->_pm->getPathinfo()->getRootLib() . '/Psn/Test/CcSelectField.php';
        require_once $this->_pm->getPathinfo()->getRootLib() . '/Psn/Test/CategoriesField.php';

        $selftester->addTestCase(new Psn_Test_RuleModel());
        $selftester->addTestCase(new Psn_Test_BccField());
        $selftester->addTestCase(new Psn_Test_BccSelectField());
        $selftester->addTestCase(new Psn_Test_CcSelectField());
        $selftester->addTestCase(new Psn_Test_CategoriesField());
    }

    /**
     * @internal param \IfwPsn_Wp_Options_Section $generalOptions
     */
    public function addOptions()
    {
        require_once $this->_pm->getPathinfo()->getRootLib() . '/IfwPsn/Wp/Options/Section.php';
        require_once $this->_pm->getPathinfo()->getRootLib() . '/IfwPsn/Wp/Options/Field/Checkbox.php';
        require_once $this->_pm->getPathinfo()->getRootLib() . '/IfwPsn/Wp/Options/Field/Textarea.php';

        $this->getOptionsManager()->addGeneralOption(new IfwPsn_Wp_Options_Field_Checkbox(
            'psn_ignore_status_inherit',
            __('Ignore post status "inherit"', 'psn'),
            __('Status "inherit" is used when post revisions get created by WordPress automatically', 'psn')
        ));
        $this->getOptionsManager()->addGeneralOption(new IfwPsn_Wp_Options_Field_Checkbox(
            'psn_hide_nonpublic_posttypes',
            __('Hide non-public post types', 'psn'),
            __('When selected, non-public post types will be excluded from rule settings form', 'psn')
        ));

        if (!$this->_pm->isPremium()) {
            $smtpOptions = new IfwPsn_Wp_Options_Section('smtp', __('SMTP', 'psn_smtp'));
            $smtpOptions->addField(new IfwPsn_Wp_Options_Field_Checkbox(
                'smtp_teaser',
                __('Activate SMTP', 'psn'),
                __('SMTP is a premium feature. You will get all configuration options to connect to your SMTP server.', 'psn')
            ));
            $this->_pm->getBootstrap()->getOptions()->addSection($smtpOptions, 12);
        }

        $placeholderFilterOptions = new IfwPsn_Wp_Options_Section('placeholders', __('Placeholders', 'psn'));

        $placeholderFilterOptions->addField(new IfwPsn_Wp_Options_Field_Textarea(
            'placeholders_filters',
            __('Placeholders filters', 'psn'),
            sprintf( __('Here you can define filters which will apply to the placeholders contents (One filter per line). You can use the <a href="%s" target="_blank">Twig filters</a>. Refer to the <a href="%s" target="_blank">documentation</a> for details.<br>Example: [post_date]|date("m/d/Y")', 'psn_smtp'),
                'http://twig.sensiolabs.org/doc/filters/index.html',
                'http://docs.ifeelweb.de/post-status-notifier/options.html#placeholders')
        ));

        $this->_pm->getBootstrap()->getOptions()->addSection($placeholderFilterOptions, 300);
    }

    /**
     * 
     */
    public function addPluginActionLinks($links, $file)
    {
        $links[] = '<a href="' . substr(IfwPsn_Wp_Proxy_Admin::getMenuUrl($this->_pm, 'index'), 1) . '">' . __('Settings', 'psn') . '</a>';
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
