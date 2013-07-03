<?php
/**
 *
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) 2012-2013 ifeelweb.de
 * @package     Psn_Admin
 */
class Psn_Admin_Metabox_Rules extends Ifw_Wp_Plugin_Admin_Menu_Metabox_Ajax
{

    /** (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initId()
     */
    protected function _initId()
    {
        return 'rules';
    }

    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initTitle()
     */
    protected function _initTitle()
    {
        return __('Your notification rules', 'psn');
    }

    /** (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initPriority()
     */
    protected function _initPriority()
    {
        return 'core';
    }

    /** (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Ajax::getAjaxResponse()
     */
    public function getAjaxResponse()
    {
        $listTable = new Psn_Admin_ListTable_Rules($this->_pm, array('metabox_embedded' => true, 'ajax' => true));

        if (isset($_POST['refresh_rows'])) {
            $html = $listTable->ajax_response();
        } else {
            $html = '<p><a href="'.  substr(Ifw_Wp_Proxy_Admin::getMenuUrl($this->_pm, 'rules', 'create'), 1) .'" class="ifw-wp-icon-plus" id="link_create_rule">'.
                __('Create new rule', 'psn') .'</a></p>';
            $html .= $listTable->fetch();
        }

        return new Ifw_Wp_Ajax_Response(true, $html);
    }

}
