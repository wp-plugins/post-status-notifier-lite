<?php
/**
 * Plugin status metabox
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Wp_Plugin_Metabox_PluginStatus extends Ifw_Wp_Plugin_Metabox_Ajax
{
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::init()
     */
    public function initAjaxResponse()
    {
        parent::initAjaxResponse();
    }
    
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initId()
     */
    protected function _initId()
    {
        return 'plugin_status';
    }
    
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initTitle()
     */
    protected function _initTitle()
    {
        return __('Plugin Status', 'ifw');
    }
    
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initPriority()
     */
    protected function _initPriority()
    {
        return 'core';
    }

    /**
     * Renders the metabox content
     * @return Ifw_Wp_Ajax_Response
     */
    public function getAjaxResponse()
    {
        $tpl = Ifw_Wp_Tpl::getInstance($this->_pm);

        $context = array(
            'ajax' => $this->getAjaxRequest(),
            'iframe_src' => Ifw_Wp_Proxy_Admin::getUrl() . Ifw_Wp_Proxy_Admin::getMenuUrl($this->_pm, 'selftest')
        );

        $timestamp = $this->_pm->getBootstrap()->getSelftester()->getTimestamp();
        if (!empty($timestamp)) {
            $timestamp = Ifw_Wp_Date::format($timestamp);
        }
        $context['timestamp'] = $timestamp;
        $status = $this->_pm->getBootstrap()->getSelftester()->getStatus();

        if ($status === true) {
            $context['status'] = 'true';
        } elseif ($status === false) {
            $context['status'] = 'false';
        } else {
            $context['status'] = 'null';
        }

        $html = $tpl->render('metabox_pluginstatus.html.twig', $context);
        $success = true;

        return new Ifw_Wp_Ajax_Response($success, $html);
    }
}
