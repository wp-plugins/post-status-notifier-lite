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
 * @subpackage Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** IfwZend_Log_Filter_Abstract */
//require_once 'IfwZend/Log/Filter/Abstract.php';

/**
 * @category   Zend
 * @package    IfwZend_Log
 * @subpackage Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class IfwZend_Log_Filter_Priority extends IfwZend_Log_Filter_Abstract
{
    /**
     * @var integer
     */
    protected $_priority;

    /**
     * @var string
     */
    protected $_operator;

    /**
     * Filter logging by $priority.  By default, it will accept any log
     * event whose priority value is less than or equal to $priority.
     *
     * @param  integer  $priority  Priority
     * @param  string   $operator  Comparison operator
     * @return void
     * @throws IfwZend_Log_Exception
     */
    public function __construct($priority, $operator = null)
    {
        if (! is_int($priority)) {
            //require_once 'IfwZend/Log/Exception.php';
            throw new IfwZend_Log_Exception('Priority must be an integer');
        }

        $this->_priority = $priority;
        $this->_operator = $operator === null ? '<=' : $operator;
    }

    /**
     * Create a new instance of IfwZend_Log_Filter_Priority
     *
     * @param  array|IfwZend_Config $config
     * @return IfwZend_Log_Filter_Priority
     */
    static public function factory($config)
    {
        $config = self::_parseConfig($config);
        $config = array_merge(array(
            'priority' => null,
            'operator' => null,
        ), $config);

        // Add support for constants
        if (!is_numeric($config['priority']) && isset($config['priority']) && defined($config['priority'])) {
            $config['priority'] = constant($config['priority']);
        }

        return new self(
            (int) $config['priority'],
            $config['operator']
        );
    }

    /**
     * Returns TRUE to accept the message, FALSE to block it.
     *
     * @param  array    $event    event data
     * @return boolean            accepted?
     */
    public function accept($event)
    {
        return version_compare($event['priority'], $this->_priority, $this->_operator);
    }
}
