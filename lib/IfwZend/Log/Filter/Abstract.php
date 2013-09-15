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
 * @package    IfwZend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** @see IfwZend_Log_Filter_Interface */
//require_once 'IfwZend/Log/Filter/Interface.php';

/** @see IfwZend_Log_FactoryInterface */
//require_once 'IfwZend/Log/FactoryInterface.php';

/**
 * @category   Zend
 * @package    IfwZend_Log
 * @subpackage Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
abstract class IfwZend_Log_Filter_Abstract
    implements IfwZend_Log_Filter_Interface, IfwZend_Log_FactoryInterface
{
    /**
     * Validate and optionally convert the config to array
     *
     * @param  array|IfwZend_Config $config IfwZend_Config or Array
     * @return array
     * @throws IfwZend_Log_Exception
     */
    static protected function _parseConfig($config)
    {
        if ($config instanceof IfwZend_Config) {
            $config = $config->toArray();
        }

        if (!is_array($config)) {
            //require_once 'IfwZend/Log/Exception.php';
            throw new IfwZend_Log_Exception('Configuration must be an array or instance of IfwZend_Config');
        }

        return $config;
    }
}
