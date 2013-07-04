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
 * @subpackage IfwZend_Controller_Action
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** IfwZend_Controller_Action_Helper_Abstract */
//require_once 'IfwZend/Controller/Action/Helper/Abstract.php';

/**
 * Helper for interacting with IfwZend_Layout objects
 *
 * @uses       IfwZend_Controller_Action_Helper_Abstract
 * @category   Zend
 * @package    IfwZend_Controller
 * @subpackage IfwZend_Controller_Action
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_Layout_Controller_Action_Helper_Layout extends IfwZend_Controller_Action_Helper_Abstract
{
    /**
     * @var IfwZend_Controller_Front
     */
    protected $_frontController;

    /**
     * @var IfwZend_Layout
     */
    protected $_layout;

    /**
     * @var bool
     */
    protected $_isActionControllerSuccessful = false;

    /**
     * Constructor
     *
     * @param  IfwZend_Layout $layout
     * @return void
     */
    public function __construct(IfwZend_Layout $layout = null)
    {
        if (null !== $layout) {
            $this->setLayoutInstance($layout);
        } else {
            /**
             * @see IfwZend_Layout
             */
            //require_once 'IfwZend/Layout.php';
            $layout = IfwZend_Layout::getMvcInstance();
        }

        if (null !== $layout) {
            $pluginClass = $layout->getPluginClass();
            $front = $this->getFrontController();
            if ($front->hasPlugin($pluginClass)) {
                $plugin = $front->getPlugin($pluginClass);
                $plugin->setLayoutActionHelper($this);
            }
        }
    }

    public function init()
    {
        $this->_isActionControllerSuccessful = false;
    }

    /**
     * Get front controller instance
     *
     * @return IfwZend_Controller_Front
     */
    public function getFrontController()
    {
        if (null === $this->_frontController) {
            /**
             * @see IfwZend_Controller_Front
             */
            //require_once 'IfwZend/Controller/Front.php';
            $this->_frontController = IfwZend_Controller_Front::getInstance();
        }

        return $this->_frontController;
    }

    /**
     * Get layout object
     *
     * @return IfwZend_Layout
     */
    public function getLayoutInstance()
    {
        if (null === $this->_layout) {
            /**
             * @see IfwZend_Layout
             */
            //require_once 'IfwZend/Layout.php';
            if (null === ($this->_layout = IfwZend_Layout::getMvcInstance())) {
                $this->_layout = new IfwZend_Layout();
            }
        }

        return $this->_layout;
    }

    /**
     * Set layout object
     *
     * @param  IfwZend_Layout $layout
     * @return IfwZend_Layout_Controller_Action_Helper_Layout
     */
    public function setLayoutInstance(IfwZend_Layout $layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * Mark Action Controller (according to this plugin) as Running successfully
     *
     * @return IfwZend_Layout_Controller_Action_Helper_Layout
     */
    public function postDispatch()
    {
        $this->_isActionControllerSuccessful = true;
        return $this;
    }

    /**
     * Did the previous action successfully complete?
     *
     * @return bool
     */
    public function isActionControllerSuccessful()
    {
        return $this->_isActionControllerSuccessful;
    }

    /**
     * Strategy pattern; call object as method
     *
     * Returns layout object
     *
     * @return IfwZend_Layout
     */
    public function direct()
    {
        return $this->getLayoutInstance();
    }

    /**
     * Proxy method calls to layout object
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        $layout = $this->getLayoutInstance();
        if (method_exists($layout, $method)) {
            return call_user_func_array(array($layout, $method), $args);
        }

        //require_once 'IfwZend/Layout/Exception.php';
        throw new IfwZend_Layout_Exception(sprintf("Invalid method '%s' called on layout action helper", $method));
    }
}
