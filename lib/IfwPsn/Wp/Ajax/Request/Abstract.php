<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  IfwPsn_Wp
 */
abstract class IfwPsn_Wp_Ajax_Request_Abstract
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
        $this->_init();
    }

    protected function _init()
    {
        $this->_initAction();
        $this->_initNonce();

        if (method_exists($this, 'getResponse')) {

            IfwPsn_Wp_Proxy_Action::add($this->getAction(), array($this, '_render'));
            if ($this instanceof IfwPsn_Wp_Ajax_Request_Public) {
                IfwPsn_Wp_Proxy_Action::add('wp_ajax_' . $this->getId(), array($this, '_render'));
            }
        }
    }

    public function _render()
    {
        if (isset($_REQUEST['nonce'])) {
            $this->_nonce = strip_tags(trim($_REQUEST['nonce']));
        }

        if (!wp_verify_nonce($this->_nonce, $this->getAction())) {
            $response = new IfwPsn_Wp_Ajax_Response(false, 'AJAX error: '. __('You do not have sufficient permissions to access this page.'));
        } else {
            $response = $this->getResponse();

        }

        $this->_output($response);
    }

    protected function _output(IfwPsn_Wp_Ajax_Response $response)
    {
        $result = array('success' => $response->getSuccess(), 'html' => $response->getHtml());

        foreach ($response->getExtra() as $key => $value) {
            $result[$key] = $value;
        }

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
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
     * @return string $_nonce
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
