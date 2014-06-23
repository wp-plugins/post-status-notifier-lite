<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Public AJAX request
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  IfwPsn_Wp_Ajax
 */
class IfwPsn_Wp_Ajax_Request_Public extends IfwPsn_Wp_Ajax_Request_Abstract
{
    /**
     * (non-PHPdoc)
     * @see IfwPsn_Wp_Ajax_Request_Abstract::_initId()
     */
    protected function _initId($id)
    {
        $this->_id = $id;
    }
    
    /**
     * (non-PHPdoc)
     * @see IfwPsn_Wp_Ajax_Request_Abstract::_initAction()
     */
    protected function _initAction()
    {
        $this->_action = 'wp_ajax_nopriv_' . $this->getId();
    }
}
