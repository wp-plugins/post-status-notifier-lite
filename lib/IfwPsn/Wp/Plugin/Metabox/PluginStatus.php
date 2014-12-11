<?php
/**
 * Plugin status metabox
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  IfwPsn_Wp
 */
require_once dirname(__FILE__) . '/Ajax.php';

class IfwPsn_Wp_Plugin_Metabox_PluginStatus extends IfwPsn_Wp_Plugin_Metabox_Ajax
{
    protected $_iframeSrc;

    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     * @param null $iframeSrc
     */
    public function __construct (IfwPsn_Wp_Plugin_Manager $pm, $iframeSrc = null)
    {
        parent::__construct($pm);

        if ($iframeSrc !== null) {
            $this->_iframeSrc = $iframeSrc;
        }
    }

    /**
     * (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Admin_Menu_Metabox_Abstract::init()
     */
    public function initAjaxResponse()
    {
        parent::initAjaxResponse();
    }
    
    /**
     * (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initId()
     */
    protected function _initId()
    {
        return 'plugin_status';
    }
    
    /**
     * (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initTitle()
     */
    protected function _initTitle()
    {
        return __('Plugin Status', 'ifw');
    }
    
    /**
     * (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initPriority()
     */
    protected function _initPriority()
    {
        return 'core';
    }

    /**
     * Renders the metabox content
     * @return IfwPsn_Wp_Ajax_Response
     */
    public function getAjaxResponse()
    {
        $tpl = IfwPsn_Wp_Tpl::getInstance($this->_pm);

        if ($this->_iframeSrc !== null) {
            $iframeSrc = $this->_iframeSrc;
        } else {
            $iframeSrc = IfwPsn_Wp_Proxy_Admin::getUrl() . IfwPsn_Wp_Proxy_Admin::getMenuUrl($this->_pm, 'selftest');
        }

        $context = array(
            'ajax' => $this->getAjaxRequest(),
            'iframe_src' => $iframeSrc,
            'img_path' => $this->_pm->getEnv()->getUrlAdminImg()
        );

        $timestamp = $this->_pm->getBootstrap()->getSelftester()->getTimestamp();
        if (!empty($timestamp)) {
            require_once $this->_pm->getPathinfo()->getRootLib() . 'IfwPsn/Wp/Date.php';
            $timestamp = IfwPsn_Wp_Date::format($timestamp);
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

        return new IfwPsn_Wp_Ajax_Response($success, $html);
    }
}
