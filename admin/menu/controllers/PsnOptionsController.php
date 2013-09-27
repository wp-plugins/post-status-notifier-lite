<?php
/**
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
class PsnOptionsController extends PsnApplicationController
{
    /**
     *
     */
    public function indexAction()
    {
        Ifw_Wp_Proxy_Script::loadAdmin('psn_options', $this->_pm->getEnv()->getUrlAdminJs() . 'options.js');

        // set up contextual help
        $help = new Ifw_Wp_Plugin_Menu_Help($this->_pm);
        $help->setTitle(__('Options', 'psn'))
            ->setHelp($this->_getHelpText())
            ->setSidebar($this->_getHelpSidebar())
            ->load();

        $this->view->options = Ifw_Wp_Options::getInstance($this->_pm);
    }

    /**
     *
     * @return string
     */
    protected function _getHelpText()
    {
        return sprintf(__('Please consider the documentation page <a href="%s" target="_blank">%s</a> for more information.', 'ifw'),
            'http://docs.ifeelweb.de/post-status-notifier/options.html',
            __('Options', 'psn'));
    }

    /**
     *
     * @return string
     */
    protected function _getHelpSidebar()
    {
        $sidebar = '<p><b>' . __('For more information:', 'ifw') . '</b></p>';
        $sidebar .= sprintf('<p><a href="%s" target="_blank">' . __('Plugin homepage', 'ifw') . '</a></p>',
            $this->_pm->getEnv()->getHomepage());
        if (!empty($this->_pm->getConfig()->plugin->docUrl)) {
            $sidebar .= sprintf('<p><a href="%s" target="_blank">' . __('Documentation', 'ifw') . '</a></p>',
                $this->_pm->getConfig()->plugin->docUrl);
        }
        return $sidebar;
    }
}

