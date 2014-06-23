<?php
/**
 *
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) ifeelweb.de
 * @package     Psn_Admin
 */
class Psn_Admin_Metabox_Rules extends IfwPsn_Wp_Plugin_Metabox_Ajax
{

    /** (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initId()
     */
    protected function _initId()
    {
        return 'rules';
    }

    /**
     * (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initTitle()
     */
    protected function _initTitle()
    {
        return __('Your notification rules', 'psn');
    }

    /** (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initPriority()
     */
    protected function _initPriority()
    {
        return 'core';
    }

    /** (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Admin_Menu_Metabox_Ajax::getAjaxResponse()
     */
    public function getAjaxResponse()
    {
        $listTable = new Psn_Admin_ListTable_Rules($this->_pm, array('metabox_embedded' => true, 'ajax' => true));

        if (isset($_POST['refresh_rows'])) {
            $html = $listTable->ajax_response();
        } else {
            $html = '<p><a href="'.  IfwPsn_Wp_Proxy_Admin::getUrl() . IfwPsn_Wp_Proxy_Admin::getMenuUrl($this->_pm, 'rules', 'create') .'" class="ifw-wp-icon-plus" id="link_create_rule">'.
                __('Create new rule', 'psn') .'</a></p>';
            $html .= $listTable->fetch();
        }

        return new IfwPsn_Wp_Ajax_Response(true, $html);
    }
}
