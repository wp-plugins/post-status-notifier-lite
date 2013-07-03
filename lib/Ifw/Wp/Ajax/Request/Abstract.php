<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
abstract class Ifw_Wp_Ajax_Request_Abstract
{
    protected $_id;
    protected $_action;
    protected $_nonce;
    protected $_callback;
    
    
    
    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->_initId($id);
        $this->_initAction();
        $this->_initNonce();
    }
    
    /**
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param string $_id
     */
    abstract protected function _initId($_id);

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }
    
    /**
     * 
     */
    abstract protected function _initAction();

    /**
     * @return the $_nonce
     */
    public function getNonce()
    {
        return $this->_nonce;
    }
    
    /**
     * 
     */
    protected function _initNonce()
    {
        if ($this->_action != null && function_exists('wp_create_nonce')) {
            $this->_nonce = wp_create_nonce($this->_action);
        }
    }

    /**
     * @return callable $callback
     */
    public function getCallback()
    {
        return $this->_callback;
    }

    /**
     * @param callable $callback
     */
    public function setCallback($callback)
    {
        $this->_callback = $callback;
    }
}
