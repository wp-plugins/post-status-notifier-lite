<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Ajax Manager
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Wp_Ajax_Manager
{
    /**
     * Instance store
     * @var array
     */
    public static $_instances = array();
    
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
     * @param $requestRoot
     * @return Ifw_Wp_Ajax_Manager
     */
    public static function getInstance($requestRoot)
    {
        if (!isset(self::$_instances[$requestRoot])) {
            self::$_instances[$requestRoot] = new self($requestRoot);
        }
        return self::$_instances[$requestRoot];
    }
    
    /**
     * 
     * @param string $requestRoot
     * @throws Ifw_Wp_Ajax_Exception
     */
    protected function __construct ($requestRoot)
    {
        if (!is_dir($requestRoot)) {
            throw new Ifw_Wp_Ajax_Exception('Invalid request root');
        }
        $this->_requestRoot = $requestRoot;
        
        $this->_initAction();
        $this->_initNonce();
    }
    
    /**
     * Checks if it is an Ajax request
     * 
     * @return boolean
     */
    public static function isAccess()
    {
        $requestInfo = pathinfo($_SERVER['REQUEST_URI']);
        if ($requestInfo['filename'] == 'admin-ajax') {
            return true;
        }
        return false;
    }
    
    /**
     * 
     */
    protected function _initAction()
    {
        if (isset($_GET['action'])) {
            $this->_action = esc_attr($_GET['action']);
        } else {
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
     * 
     * 
     * @return boolean
     */
    public function isValid()
    {
        if (file_exists($this->getRequestFile())) {
            return true;
        }
        return false;
    }
    
    /**
     * @return boolean
     */
    public function getRequestFile()
    {
        $dir = $this->_requestRoot . 'ajax';
        $requestFile = $dir . DIRECTORY_SEPARATOR . $this->getAction() . '.php';
                
        $info = pathinfo($requestFile);
        
        if ($info['dirname'] == $dir) {
            // security check
            return $requestFile;
        }
        return '';
    }
    
    /**
     * Loads the Ajax requests
     */
    public function load(Ifw_Wp_Plugin_Manager $pm)
    {
        if ($this->isValid()) {

            $requests = include_once $this->getRequestFile();

            if (!is_array($requests)) {
                $requests = array($requests);
            }
            foreach ($requests as $request) {

                if ($request instanceof Ifw_Wp_Ajax_Request_Abstract) {
                    $dispatcher = new Ifw_Wp_Ajax_Request_Dispatcher($request);
                    $dispatcher->setNonce($this->getNonce());
                    $dispatcher->dispatch();
                }
            }
        }
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