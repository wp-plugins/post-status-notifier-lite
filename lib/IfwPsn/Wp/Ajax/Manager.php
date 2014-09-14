<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Ajax Manager
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  IfwPsn_Wp
 */
class IfwPsn_Wp_Ajax_Manager
{
    /**
     * Instance store
     * @var array
     */
    public static $_instances = array();

    /**
     * @var IfwPsn_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var array
     */
    protected $_registeredRequests = array();
    
    /**
     * @var string
     */
    protected $_requestRoot;
    
    /**
     * @var string
     */
    protected $_action;
    
    /**
     * @var string
     */
    protected $_nonce;



    /**
     * Retrieves singleton object
     * @param IfwPsn_Wp_Plugin_Manager $pm
     * @return IfwPsn_Wp_Ajax_Manager
     */
    public static function getInstance(IfwPsn_Wp_Plugin_Manager $pm)
    {
        if (!isset(self::$_instances[$pm->getAbbr()])) {
            self::$_instances[$pm->getAbbr()] = new self($pm);
        }
        return self::$_instances[$pm->getAbbr()];
    }

    /**
     *
     * @param IfwPsn_Wp_Plugin_Manager $pm
     * @internal param string $requestRoot
     */
    protected function __construct(IfwPsn_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
        $this->_initAction();
        $this->_initNonce();
    }

    /**
     * @param $request
     */
    public function registerRequest($request)
    {
        array_push($this->_registeredRequests, $request);
    }

    public function resetRequests()
    {
        $this->_registeredRequests = array();
    }
    
    /**
     * 
     */
    protected function _initAction()
    {
        if (isset($_GET['action'])) {
            $this->_action = esc_attr($_GET['action']);
        } elseif (isset($_REQUEST['action'])) {
            $this->_action = esc_attr($_REQUEST['action']);
        }
    }
    
    /**
     * 
     */
    protected function _initNonce()
    {
        if (isset($_REQUEST['nonce'])) {
            $this->_nonce = strip_tags(trim($_REQUEST['nonce']));
        }
    }

    /**
     * @param $requestPath
     * @return bool
     */
    public function isValid($requestPath)
    {
        $info = pathinfo($requestPath);

        if ($info['basename'] == $this->getAction() . '.php') {
            return true;
        }
        return false;
    }
    
    /**
     * Loads the Ajax request
     */
    public function load()
    {
        foreach ($this->_registeredRequests as $requestPath) {

            if ($this->isValid($requestPath)) {

                // set $pm for use in request file
                $pm = $this->_pm;
                $requests = include_once $requestPath;

                if (!is_array($requests)) {
                    $requests = array($requests);
                }
                foreach ($requests as $request) {

                    if ($request instanceof IfwPsn_Wp_Ajax_Request_Abstract) {
                        $dispatcher = new IfwPsn_Wp_Ajax_Request_Dispatcher($request);
                        $dispatcher->setNonce($this->getNonce());
                        $dispatcher->dispatch();
                    }
                }
                // the valid request has been dispatched
                break;
            }
        }

        $this->resetRequests();
    }
    
    /**
     * @return the $_action
     */
    public function getAction()
    {
        return $this->_action;
    }
    
    /**
     * @return the $_nonce
     */
    public function getNonce()
    {
        return $this->_nonce;
    }
}