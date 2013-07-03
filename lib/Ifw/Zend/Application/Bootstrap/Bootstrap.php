<?php
/**
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
class Ifw_Zend_Application_Bootstrap_Bootstrap extends IfwZend_Application_Bootstrap_Bootstrap
{
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;
    
    /**
     *
     * @param IfwZend_Application $application
     */
    public function __construct($application)
    {

        parent::__construct($application);
    
        $this->_pm = $this->getApplication()->getOption('pluginmanager');

        // init the custom front controller instance
        Ifw_Zend_Controller_Front::getInstance();

        IfwZend_Controller_Front::getInstance()->returnResponse(true);
        IfwZend_Controller_Front::getInstance()->setDispatcher(new Ifw_Zend_Controller_Dispatcher_Wp($this->_pm));
    }

    /**
     * Inits the controller object on bootstrap to place WP actions on before page load
     *
     * @throws IfwZend_Application_Bootstrap_Exception
     */
    public function initController()
    {
        $front   = $this->getResource('FrontController');
        $default = $front->getDefaultModule();
        if (null === $front->getControllerDirectory($default)) {
            throw new IfwZend_Application_Bootstrap_Exception(
                'No default controller directory registered with front controller'
            );
        }

        $front->setParam('bootstrap', $this);
        $front->initController();
    }

    /**
     * Dispatches response on already initialized controller
     *
     * @return string
     */
    public function run()
    {
        // execute controller action on load-page
        $front   = $this->getResource('FrontController');
        $response = $front->dispatch();
        if ($front->returnResponse()) {
            return $response;
        }
    }
    
    /**
     * Init custom router and plugins
     */
    protected function _initPlugin()
    {
        if ($this->_pm->isExactAdminAccess()) {

            $this->bootstrap('frontController');
            $front = $this->getResource('FrontController');

            // set custom router
            $front->setRouter(new Ifw_Zend_Controller_Router_WpRewrite());
            
            // launch the custom router to support request vars for controller / action
            $router = $front->getRouter();
            $router->addRoute('requestVars', new Ifw_Zend_Controller_Router_Route_RequestVars($this->_pm));
    
            // launch the wp request dispatcher
            $front->registerPlugin(new Ifw_Zend_Controller_Plugin_WpRequestDispatcher($this->_pm));
        }
    }
}