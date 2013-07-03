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

/** IfwZend_Log */
//require_once 'IfwZend/Logger.php';

/** IfwZend_Log_Writer_Abstract */
//require_once 'IfwZend/Log/Writer/Abstract.php';

/** IfwZend_Log_Formatter_Firebug */
//require_once 'IfwZend/Log/Formatter/Firebug.php';

/** IfwZend_Wildfire_Plugin_FirePhp */
//require_once 'IfwZend/Wildfire/Plugin/FirePhp.php';

/**
 * Writes log messages to the Firebug Console via FirePHP.
 *
 * @category   Zend
 * @package    IfwZend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_Log_Writer_Firebug extends IfwZend_Log_Writer_Abstract
{
    /**
     * Maps logging priorities to logging display styles
     *
     * @var array
     */
    protected $_priorityStyles = array(IfwZend_Log::EMERG  => IfwZend_Wildfire_Plugin_FirePhp::ERROR,
                                       IfwZend_Log::ALERT  => IfwZend_Wildfire_Plugin_FirePhp::ERROR,
                                       IfwZend_Log::CRIT   => IfwZend_Wildfire_Plugin_FirePhp::ERROR,
                                       IfwZend_Log::ERR    => IfwZend_Wildfire_Plugin_FirePhp::ERROR,
                                       IfwZend_Log::WARN   => IfwZend_Wildfire_Plugin_FirePhp::WARN,
                                       IfwZend_Log::NOTICE => IfwZend_Wildfire_Plugin_FirePhp::INFO,
                                       IfwZend_Log::INFO   => IfwZend_Wildfire_Plugin_FirePhp::INFO,
                                       IfwZend_Log::DEBUG  => IfwZend_Wildfire_Plugin_FirePhp::LOG);

    /**
     * The default logging style for un-mapped priorities
     *
     * @var string
     */
    protected $_defaultPriorityStyle = IfwZend_Wildfire_Plugin_FirePhp::LOG;

    /**
     * Flag indicating whether the log writer is enabled
     *
     * @var boolean
     */
    protected $_enabled = true;

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct()
    {
        if (php_sapi_name() == 'cli') {
            $this->setEnabled(false);
        }

        $this->_formatter = new IfwZend_Log_Formatter_Firebug();
    }

    /**
     * Create a new instance of IfwZend_Log_Writer_Firebug
     *
     * @param  array|IfwZend_Config $config
     * @return IfwZend_Log_Writer_Firebug
     */
    static public function factory($config)
    {
        return new self();
    }

    /**
     * Enable or disable the log writer.
     *
     * @param boolean $enabled Set to TRUE to enable the log writer
     * @return boolean The previous value.
     */
    public function setEnabled($enabled)
    {
        $previous = $this->_enabled;
        $this->_enabled = $enabled;
        return $previous;
    }

    /**
     * Determine if the log writer is enabled.
     *
     * @return boolean Returns TRUE if the log writer is enabled.
     */
    public function getEnabled()
    {
        return $this->_enabled;
    }

    /**
     * Set the default display style for user-defined priorities
     *
     * @param string $style The default log display style
     * @return string Returns previous default log display style
     */
    public function setDefaultPriorityStyle($style)
    {
        $previous = $this->_defaultPriorityStyle;
        $this->_defaultPriorityStyle = $style;
        return $previous;
    }

    /**
     * Get the default display style for user-defined priorities
     *
     * @return string Returns the default log display style
     */
    public function getDefaultPriorityStyle()
    {
        return $this->_defaultPriorityStyle;
    }

    /**
     * Set a display style for a logging priority
     *
     * @param int $priority The logging priority
     * @param string $style The logging display style
     * @return string|boolean The previous logging display style if defined or TRUE otherwise
     */
    public function setPriorityStyle($priority, $style)
    {
        $previous = true;
        if (array_key_exists($priority,$this->_priorityStyles)) {
            $previous = $this->_priorityStyles[$priority];
        }
        $this->_priorityStyles[$priority] = $style;
        return $previous;
    }

    /**
     * Get a display style for a logging priority
     *
     * @param int $priority The logging priority
     * @return string|boolean The logging display style if defined or FALSE otherwise
     */
    public function getPriorityStyle($priority)
    {
        if (array_key_exists($priority,$this->_priorityStyles)) {
            return $this->_priorityStyles[$priority];
        }
        return false;
    }

    /**
     * Log a message to the Firebug Console.
     *
     * @param array $event The event data
     * @return void
     */
    protected function _write($event)
    {
        if (!$this->getEnabled()) {
            return;
        }

        if (array_key_exists($event['priority'],$this->_priorityStyles)) {
            $type = $this->_priorityStyles[$event['priority']];
        } else {
            $type = $this->_defaultPriorityStyle;
        }

        $message = $this->_formatter->format($event);

        $label = isset($event['firebugLabel'])?$event['firebugLabel']:null;

        IfwZend_Wildfire_Plugin_FirePhp::getInstance()->send($message,
                                                          $label,
                                                          $type,
                                                          array('traceOffset'=>4,
                                                                'fixZendLogOffsetIfApplicable'=>true));
    }
}
