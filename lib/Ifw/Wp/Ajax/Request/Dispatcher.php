<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Ajax request dispatcher
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp_Ajax
 */
class Ifw_Wp_Ajax_Request_Dispatcher
{
    /**
     * @var Ifw_Wp_Ajax_Request_Abstract
     */
    protected $_request;
    
    /**
     * @var string
     */
    protected $_nonce;
    
    /**
     * @var array
     */
    protected $_callback;
    
    
    
    /**
     * 
     * @param Ifw_Wp_Ajax_Request_Abstract $request
     */
    public function __construct (Ifw_Wp_Ajax_Request_Abstract $request)
    {
        $this->_request = $request;
    }
    
    /**
     * @param string $_nonce
     */
    public function setNonce($_nonce)
    {
        $this->_nonce = $_nonce;
    }

    /**
     * 
     */
    public function dispatch()
    {
        if (is_callable($this->_request->getCallback())) {
            $this->addRequest($this->_request);
        } else {
            $this->output(new Ifw_Wp_Ajax_Response(false, __('Invalid access')));            
        }
    }

    /**
     *
     * @param Ifw_Wp_Ajax_Request_Abstract $request
     */
    public function addRequest(Ifw_Wp_Ajax_Request_Abstract $request)
    {
        $this->_callback = $request->getCallback();
        Ifw_Wp_Proxy_Action::add($request->getAction(), array($this, 'execute'));
    }
    
    /**
     * @param Ifw_Wp_Ajax_Response $response
     */
    public function output(Ifw_Wp_Ajax_Response $response)
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
     * 
     */
    public function execute()
    {
        if (!wp_verify_nonce($this->_nonce, $this->_request->getAction())) {
            $response = new Ifw_Wp_Ajax_Response(false, 'AJAX error: '. __('You do not have sufficient permissions to access this page.'));
        } else {
            $response = call_user_func($this->_callback);
        }
        $this->output($response);
    }
}
