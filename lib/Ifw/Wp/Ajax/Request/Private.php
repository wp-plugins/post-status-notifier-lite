<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Private AJAX request (for logged in users only)
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp_Ajax
 */
class Ifw_Wp_Ajax_Request_Private extends Ifw_Wp_Ajax_Request_Abstract
{
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Ajax_Request_Abstract::_initId()
     */
    protected function _initId($id)
    {
        $this->_id = $id;
    }
    
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Ajax_Request_Abstract::_initAction()
     */
    protected function _initAction()
    {
        $this->_action = 'wp_ajax_' . $this->getId();
    }
}
