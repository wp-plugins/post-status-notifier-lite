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
 * @package    IfwZend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** IfwZend_View_Helper_Abstract */
//require_once 'IfwZend/View/Helper/Abstract.php';

/**
 * Helper for interacting with UserAgent instance
 *
 * @package    IfwZend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_View_Helper_UserAgent extends IfwZend_View_Helper_Abstract
{
    /**
     * UserAgent instance
     *
     * @var IfwZend_Http_UserAgent
     */
    protected $_userAgent = null;

    /**
     * Helper method: retrieve or set UserAgent instance
     *
     * @param  null|IfwZend_Http_UserAgent $userAgent
     * @return IfwZend_Http_UserAgent
     */
    public function userAgent(IfwZend_Http_UserAgent $userAgent = null)
    {
        if (null !== $userAgent) {
            $this->setUserAgent($userAgent);
        }
        return $this->getUserAgent();
    }

    /**
     * Set UserAgent instance
     *
     * @param  IfwZend_Http_UserAgent $userAgent
     * @return IfwZend_View_Helper_UserAgent
     */
    public function setUserAgent(IfwZend_Http_UserAgent $userAgent)
    {
        $this->_userAgent = $userAgent;
        return $this;
    }

    /**
     * Retrieve UserAgent instance
     *
     * If none set, instantiates one using no configuration
     *
     * @return IfwZend_Http_UserAgent
     */
    public function getUserAgent()
    {
        if (null === $this->_userAgent) {
            //require_once 'IfwZend/Http/UserAgent.php';
            $this->setUserAgent(new IfwZend_Http_UserAgent());
        }
        return $this->_userAgent;
    }
}
