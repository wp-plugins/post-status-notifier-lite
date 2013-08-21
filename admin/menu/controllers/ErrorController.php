<?php
/**
 * Error controller
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class ErrorController extends IfwZend_Controller_Action
{
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;



    public function init()
    {
        parent::init();
        $this->_pm = Ifw_Wp_Plugin_Manager::getInstance('Psn');
    }

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = __('You have reached the error page', 'ifw');
            return;
        }

        switch ($errors->type) {
            case IfwZend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case IfwZend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case IfwZend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                // $this->getResponse()->setHttpResponseCode(404);
                $priority = IfwZend_Log::NOTICE;
                $this->view->message = __('Page not found', 'ifw');
                break;
            default:
                // application error
                // $this->getResponse()->setHttpResponseCode(500);
                $priority = IfwZend_Log::CRIT;
                $this->view->message = __('Application error', 'ifw');
                break;
        }
        
        // Log exception        
        $this->_pm->getLogger()->error($this->view->message);
        $this->_pm->getLogger()->error($errors->exception);
        $this->_pm->getLogger()->error('Request Parameters', $priority, $errors->request->getParams());        
        $this->_pm->getLogger()->error(print_r($errors->request->getParams(), true));
        
        
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request = $errors->request;
        $this->view->exception = $errors->exception;
        // conditionally display exceptions in dev env
        $this->view->displayExceptions = $this->getInvokeArg('displayExceptions');
        
        $this->view->langHeadline = __('An error occurred', 'ifw');
    }

}
