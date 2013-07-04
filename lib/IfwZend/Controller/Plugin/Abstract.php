<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    IfwZend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @category   Zend
 * @package    IfwZend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class IfwZend_Controller_Plugin_Abstract
{
    /**
     * @var IfwZend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @var IfwZend_Controller_Response_Abstract
     */
    protected $_response;

    /**
     * Set request object
     *
     * @param IfwZend_Controller_Request_Abstract $request
     * @return IfwZend_Controller_Plugin_Abstract
     */
    public function setRequest(IfwZend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Get request object
     *
     * @return IfwZend_Controller_Request_Abstract $request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Set response object
     *
     * @param IfwZend_Controller_Response_Abstract $response
     * @return IfwZend_Controller_Plugin_Abstract
     */
    public function setResponse(IfwZend_Controller_Response_Abstract $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Get response object
     *
     * @return IfwZend_Controller_Response_Abstract $response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Called before IfwZend_Controller_Front begins evaluating the
     * request against its routes.
     *
     * @param IfwZend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeStartup(IfwZend_Controller_Request_Abstract $request)
    {}

    /**
     * Called after IfwZend_Controller_Router exits.
     *
     * Called after IfwZend_Controller_Front exits from the router.
     *
     * @param  IfwZend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(IfwZend_Controller_Request_Abstract $request)
    {}

    /**
     * Called before IfwZend_Controller_Front enters its dispatch loop.
     *
     * @param  IfwZend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(IfwZend_Controller_Request_Abstract $request)
    {}

    /**
     * Called before an action is dispatched by IfwZend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link IfwZend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
     *
     * @param  IfwZend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(IfwZend_Controller_Request_Abstract $request)
    {}

    /**
     * Called after an action is dispatched by IfwZend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior. By altering the
     * request and resetting its dispatched flag (via
     * {@link IfwZend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * a new action may be specified for dispatching.
     *
     * @param  IfwZend_Controller_Request_Abstract $request
     * @return void
     */
    public function postDispatch(IfwZend_Controller_Request_Abstract $request)
    {}

    /**
     * Called before IfwZend_Controller_Front exits its dispatch loop.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {}
}
