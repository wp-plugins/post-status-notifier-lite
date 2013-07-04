<?php
/**
 * Prepares custom controller name
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
class Ifw_Zend_Controller_Plugin_WpRequestDispatcher extends IfwZend_Controller_Plugin_Abstract
{
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;
    
    
    
    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public function __construct(Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
    }
    
    /**
     * Sets custom controller name
     * 
     * @param IfwZend_Controller_Request_Abstract $request
     * @return bool
     */
    public function preDispatch(IfwZend_Controller_Request_Abstract $request)
    {
        $response = $this->getResponse();
        $response->headersSentThrowsException = false;

        try {
            if ($request->getControllerName() == 'error') {
                return;
            }
            
            $customController = $this->_getCustomController($request);

            if ($customController != false) {
                // set the custom controller if exists
                $request->setControllerName($customController);
            }

        } catch (Exception $e) {
            // Repoint the request to the default error handler
            $request->setModuleName('default');
            $request->setControllerName('error');
            $request->setActionName('error');

            // Set up the error handler
            $error = new IfwZend_Controller_Plugin_ErrorHandler($this->_pm);
            $error->type = IfwZend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER;
            $error->request = clone($request);
            $error->exception = $e;
            $request->setParam('error_handler', $error);
        }
    }
    
    /**
     * Retrieves custom controller name
     *  
     * @param IfwZend_Controller_Request_Abstract $request
     * @return string|boolean
     */
    protected function _getCustomController($request)
    {
        $controllerName = $this->_pm->getAbbr() .'-'. $request->get('controller');
        return $controllerName;
    }
}