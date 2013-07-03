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
 * @package    IfwZend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see IfwZend_Application_Resource_ResourceAbstract
 */
//require_once 'IfwZend/Application/Resource/ResourceAbstract.php';


/**
 * Resource for initializing the locale
 *
 * @uses       IfwZend_Application_Resource_ResourceAbstract
 * @category   Zend
 * @package    IfwZend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_Application_Resource_Log
    extends IfwZend_Application_Resource_ResourceAbstract
{
    /**
     * @var IfwZend_Log
     */
    protected $_log;

    /**
     * Defined by IfwZend_Application_Resource_Resource
     *
     * @return IfwZend_Log
     */
    public function init()
    {
        return $this->getLog();
    }

    /**
     * Attach logger
     *
     * @param  IfwZend_Log $log
     * @return IfwZend_Application_Resource_Log
     */
    public function setLog(IfwZend_Log $log)
    {
        $this->_log = $log;
        return $this;
    }

    public function getLog()
    {
        if (null === $this->_log) {
            $options = $this->getOptions();
            $log = IfwZend_Log::factory($options);
            $this->setLog($log);
        }
        return $this->_log;
    }
}
