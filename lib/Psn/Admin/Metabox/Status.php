<?php
/**
 *
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) 2012-2013 ifeelweb.de
 * @package     Psn_Admin
 */
class Psn_Admin_Metabox_Status extends Ifw_Wp_Plugin_Admin_Menu_Metabox_PluginInfo
{
    public function __construct (Ifw_Wp_Plugin_Manager $pm)
    {
        parent::__construct($pm);

        Ifw_Wp_Proxy_Action::add('psn_plugininfo_before_version', array($this, 'addStatusBlock'));
    }
    
    /**
     * Adds block about plugin status
     */
    public function addStatusBlock()
    {
        $label = __('Status');
        $content = '';

        $model = new Psn_Model_Rule();

        if ($model->exists()) {
            $iconClass = 'ok';
            $content .= __('Plugin is ready.', 'ifw');
        } else {
            $iconClass = 'error';
            $content .= __('Plugin is not ready.', 'ifw') . '<br />';
            $content .=  __('The required database table does not exist.', 'psn');
        }
    
        $this->addBlock('status', $label, $content, $iconClass);
    }
}
