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
 * @subpackage IfwZend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see IfwZend_Controller_Action
 */
//require_once 'IfwZend/Controller/Action.php';

/**
 * @category   Zend
 * @package    IfwZend_Controller
 * @subpackage IfwZend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class IfwZend_Controller_Action_Helper_Abstract
{
    /**
     * $_actionController
     *
     * @var IfwZend_Controller_Action $_actionController
     */
    protected $_actionController = null;

    /**
     * @var mixed $_frontController
     */
    protected $_frontController = null;

    /**
     * setActionController()
     *
     * @param  IfwZend_Controller_Action $actionController
     * @return IfwZend_Controller_ActionHelper_Abstract Provides a fluent interface
     */
    public function setActionController(IfwZend_Controller_Action $actionController = null)
    {
        $this->_actionController = $actionController;
        return $this;
    }

    /**
     * Retrieve current action controller
     *
     * @return IfwZend_Controller_Action
     */
    public function getActionController()
    {
        return $this->_actionController;
    }

    /**
     * Retrieve front controller instance
     *
     * @return IfwZend_Controller_Front
     */
    public function getFrontController()
    {
        return IfwZend_Controller_Front::getInstance();
    }

    /**
     * Hook into action controller initialization
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Hook into action controller preDispatch() workflow
     *
     * @return void
     */
    public function preDispatch()
    {
    }

    /**
     * Hook into action controller postDispatch() workflow
     *
     * @return void
     */
    public function postDispatch()
    {
    }

    /**
     * getRequest() -
     *
     * @return IfwZend_Controller_Request_Abstract $request
     */
    public function getRequest()
    {
        $controller = $this->getActionController();
        if (null === $controller) {
            $controller = $this->getFrontController();
        }

        return $controller->getRequest();
    }

    /**
     * getResponse() -
     *
     * @return IfwZend_Controller_Response_Abstract $response
     */
    public function getResponse()
    {
        $controller = $this->getActionController();
        if (null === $controller) {
            $controller = $this->getFrontController();
        }

        return $controller->getResponse();
    }

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        $fullClassName = get_class($this);
        if (strpos($fullClassName, '_') !== false) {
            $helperName = strrchr($fullClassName, '_');
            return ltrim($helperName, '_');
        } elseif (strpos($fullClassName, '\\') !== false) {
            $helperName = strrchr($fullClassName, '\\');
            return ltrim($helperName, '\\');
        } else {
            return $fullClassName;
        }
    }
}