<?php
/**
 * ifeelweb.de Wordpress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
class Ifw_Zend_Controller_Front extends IfwZend_Controller_Front
{
    /**
     * Overwrite getInstance to use custom front controller
     *
     * @return Ifw_Zend_Controller_Front|IfwZend_Controller_Front|null
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function initRouter(Ifw_Wp_Plugin_Manager $pm)
    {
        if (!$this->getRouter()->hasRoute('requestVars')) {
            $this->getRouter()->addRoute('requestVars', new Ifw_Zend_Controller_Router_Route_RequestVars($pm));
        }
        return $this;
    }

    /**
     * Dispatch an HTTP request to a controller/action.
     *
     * @param IfwZend_Controller_Request_Abstract|null $request
     * @param IfwZend_Controller_Response_Abstract|null $response
     * @return void|IfwZend_Controller_Response_Abstract Returns response object if returnResponse() is true
     */
    public function dispatch(IfwZend_Controller_Request_Abstract $request = null, IfwZend_Controller_Response_Abstract $response = null)
    {

        if ($this->getDispatcher() instanceof Ifw_Zend_Controller_Dispatcher_Wp &&
            !($this->getDispatcher()->getController() instanceof IfwZend_Controller_Action_Interface)) {

            // skip if controller object already exists, already done by initController

            if (!$this->getParam('noErrorHandler') && !$this->_plugins->hasPlugin('IfwZend_Controller_Plugin_ErrorHandler')) {
                // Register with stack index of 100
                //require_once 'IfwZend/Controller/Plugin/ErrorHandler.php';
                $this->_plugins->registerPlugin(new IfwZend_Controller_Plugin_ErrorHandler(), 100);
            }

            if (!$this->getParam('noViewRenderer') && !IfwZend_Controller_Action_HelperBroker::hasHelper('viewRenderer')) {
                //require_once 'IfwZend/Controller/Action/Helper/ViewRenderer.php';
                IfwZend_Controller_Action_HelperBroker::getStack()->offsetSet(-80, new IfwZend_Controller_Action_Helper_ViewRenderer());
            }

            /**
             * Instantiate default request object (HTTP version) if none provided
             */
            if (null !== $request) {
                $this->setRequest($request);
            } elseif ((null === $request) && (null === ($request = $this->getRequest()))) {
                //require_once 'IfwZend/Controller/Request/Http.php';
                $request = new IfwZend_Controller_Request_Http();
                $this->setRequest($request);
            }

           /**
            * Set base URL of request object, if available
            */
            if (is_callable(array($this->_request, 'setBaseUrl'))) {
                if (null !== $this->_baseUrl) {
                    $this->_request->setBaseUrl($this->_baseUrl);
                }
            }

            /**
             * Instantiate default response object (HTTP version) if none provided
             */
            if (null !== $response) {
                $this->setResponse($response);
            } elseif ((null === $this->_response) && (null === ($this->_response = $this->getResponse()))) {
                //require_once 'IfwZend/Controller/Response/Http.php';
                $response = new IfwZend_Controller_Response_Http();
                $this->setResponse($response);
            }

            /**
             * Register request and response objects with plugin broker
             */
            $this->_plugins
                 ->setRequest($this->_request)
                 ->setResponse($this->_response);

        } // END: skip if controller object already exists

        // PROCEED with standard dispatch routine

        /**
         * Initialize router
         */
        $router = $this->getRouter();
        $router->setParams($this->getParams());

        /**
         * Initialize dispatcher
         */
        $dispatcher = $this->getDispatcher();
        $dispatcher->setParams($this->getParams())
            ->setResponse($this->_response);

        // Begin dispatch
        try {
            /**
             * Route request to controller/action, if a router is provided
             */

            /**
             * Notify plugins of router startup
             */
            $this->_plugins->routeStartup($this->_request);

            try {
                $router->route($this->_request);
            }  catch (Exception $e) {
                if ($this->throwExceptions()) {
                    throw $e;
                }

                $this->_response->setException($e);
            }

            /**
             * Notify plugins of router completion
             */
            $this->_plugins->routeShutdown($this->_request);

            /**
             * Notify plugins of dispatch loop startup
             */
            $this->_plugins->dispatchLoopStartup($this->_request);

            /**
             *  Attempt to dispatch the controller/action. If the $this->_request
             *  indicates that it needs to be dispatched, move to the next
             *  action in the request.
             */
            do {
                $this->_request->setDispatched(true);

                /**
                 * Notify plugins of dispatch startup
                 */
                $this->_plugins->preDispatch($this->_request);

                /**
                 * Skip requested action if preDispatch() has reset it
                 */
                if (!$this->_request->isDispatched()) {
                    continue;
                }

                /**
                 * Dispatch request
                 */
                try {
                    $dispatcher->dispatch($this->_request, $this->_response);
                } catch (Exception $e) {
                    if ($this->throwExceptions()) {
                        throw $e;
                    }
                    $this->_response->setException($e);
                }

                /**
                 * Notify plugins of dispatch completion
                 */
                $this->_plugins->postDispatch($this->_request);
            } while (!$this->_request->isDispatched());
        } catch (Exception $e) {
            if ($this->throwExceptions()) {
                throw $e;
            }

            $this->_response->setException($e);
        }

        /**
         * Notify plugins of dispatch loop completion
         */
        try {
            $this->_plugins->dispatchLoopShutdown();
        } catch (Exception $e) {
            if ($this->throwExceptions()) {
                throw $e;
            }

            $this->_response->setException($e);
        }

        if ($this->returnResponse()) {
            return $this->_response;
        }

        $this->_response->sendResponse();
    }

    /**
     * @param IfwZend_Controller_Request_Abstract $request
     * @param IfwZend_Controller_Response_Abstract $response
     * @throws Exception
     */
    public function initController(Ifw_Wp_Plugin_Manager $pm, IfwZend_Controller_Request_Abstract $request = null, IfwZend_Controller_Response_Abstract $response = null)
    {
        if (!$this->getParam('noErrorHandler') && !$this->_plugins->hasPlugin('IfwZend_Controller_Plugin_ErrorHandler')) {
            // Register with stack index of 100
            //require_once 'IfwZend/Controller/Plugin/ErrorHandler.php';
            $this->_plugins->registerPlugin(new IfwZend_Controller_Plugin_ErrorHandler(), 100);
        }

        if (!$this->getParam('noViewRenderer') && !IfwZend_Controller_Action_HelperBroker::hasHelper('viewRenderer')) {
            //require_once 'IfwZend/Controller/Action/Helper/ViewRenderer.php';
            IfwZend_Controller_Action_HelperBroker::getStack()->offsetSet(-80, new IfwZend_Controller_Action_Helper_ViewRenderer());
        }

        /**
         * Instantiate default request object (HTTP version) if none provided
         */
        if (null !== $request) {
            $this->setRequest($request);
        } elseif ((null === $request) && (null === ($request = $this->getRequest()))) {
            //require_once 'IfwZend/Controller/Request/Http.php';
            $request = new IfwZend_Controller_Request_Http();

            $this->setRequest($request);
        }
        $request->setActionKey($pm->getConfig()->getActionKey());

        /**
         * Set base URL of request object, if available
         */
        if (is_callable(array($this->_request, 'setBaseUrl'))) {
            if (null !== $this->_baseUrl) {
                $this->_request->setBaseUrl($this->_baseUrl);
            }
        }

        /**
         * Instantiate default response object (HTTP version) if none provided
         */
        if (null !== $response) {
            $this->setResponse($response);
        } elseif ((null === $this->_response) && (null === ($this->_response = $this->getResponse()))) {
            //require_once 'IfwZend/Controller/Response/Http.php';
            $response = new IfwZend_Controller_Response_Http();
            $this->setResponse($response);
        }

        //Ifw_Wp_Proxy_Action::doPlugin($pm, 'before_controller_init', $this);

        /**
         * Register request and response objects with plugin broker
         */
        $this->_plugins
            ->setRequest($this->_request)
            ->setResponse($this->_response);

        Ifw_Wp_Proxy_Action::doPlugin($pm, 'before_controller_init', $this);

        /**
         * Initialize router
         */
        $router = $this->getRouter();
        $router->setParams($this->getParams());

        /**
         * Initialize dispatcher
         */
        $dispatcher = $this->getDispatcher();
        $dispatcher->setParams($this->getParams())
            ->setResponse($this->_response);

        // Begin dispatch
        try {
            /**
             * Route request to controller/action, if a router is provided
             */

            /**
             * Notify plugins of router startup
             */
            $this->_plugins->routeStartup($this->_request);

            try {
                $router->route($this->_request);
            }  catch (Exception $e) {
                throw $e;
            }

            /**
             * Needed for custom route RequestVars
             */
            $this->_plugins->routeShutdown($this->_request);

            /**
             * skip plugins dispatchLoopStartup on initController
             */
            //$this->_plugins->dispatchLoopStartup($this->_request);

            /**
             *  Attempt to dispatch the controller/action. If the $this->_request
             *  indicates that it needs to be dispatched, move to the next
             *  action in the request.
             */
            do {
                $this->_request->setDispatched(true);

                /**
                 * skip plugins preDispatch on initController
                 */
                $this->_plugins->preDispatch($this->_request);

                /**
                 * Skip requested action if preDispatch() has reset it
                 */
                //if (!$this->_request->isDispatched()) {
                //    continue;
                //}

                /**
                 * init controller
                 */
                try {
                    // this will add custom WP action to the controller object
                    $dispatcher->initController($this->_request, $this->_response);
                } catch (Exception $e) {
                    throw $e;
                }

                /**
                 * skip plugins postDispatch on initController
                 */
                //$this->_plugins->postDispatch($this->_request);
            } while (!$this->_request->isDispatched());
        } catch (Exception $e) {
            throw $e;
        }

    }
}
