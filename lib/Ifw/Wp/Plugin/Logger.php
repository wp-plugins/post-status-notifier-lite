<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Logger
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2012-2013 ifeelweb.de
 * @version   $Id$
 * @package   Ifw_Wp_Plugin
 */
class Ifw_Wp_Plugin_Logger extends IfwZend_Log
{
    /**
     * Instance store to separate objects for multiple active plugins
     * @var array
     */
    public static $_instances = array();

    /**
     * @var array
     */
    public static $priorityInfo = array(
        IfwZend_Log::EMERG => 'Emergency',
        IfwZend_Log::ALERT => 'Alert',
        IfwZend_Log::CRIT => 'Critical',
        IfwZend_Log::ERR => 'Error',
        IfwZend_Log::WARN => 'Warning',
        IfwZend_Log::NOTICE => 'Notice',
        IfwZend_Log::INFO => 'Informational',
        IfwZend_Log::DEBUG => 'Debug',
    );

    /**
     * @var string
     */
    protected static $_defaultName = 'Default';


    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;


    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     * @param null $name
     * @throws Ifw_Wp_Plugin_Logger_Exception
     * @internal param \Ifw_Wp_Pathinfo_Plugin $pluginPathinfo
     * @internal param \Ifw_Wp_Plugin_Config $config
     * @return Ifw_Wp_Plugin_Logger
     */
    public static function getInstance(Ifw_Wp_Plugin_Manager $pm, $name = null)
    {
        if ($name === null) {
            $name = self::$_defaultName;
        }

        if (!isset(self::$_instances[$pm->getAbbr()][$name])) {
            self::factory($pm, new IfwZend_Log_Writer_Null(), $name);
        }
        return self::$_instances[$pm->getAbbr()][$name];
    }

    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     * @param IfwZend_Log_FactoryInterface $writer
     * @param null $name Loggername
     * @return Ifw_Wp_Plugin_Logger
     */
    public static function factory(Ifw_Wp_Plugin_Manager $pm, IfwZend_Log_FactoryInterface $writer, $name = null)
    {
        if ($name === null) {
            $name = self::$_defaultName;
        }

        if (!isset(self::$_instances[$pm->getAbbr()][$name])) {
            // create logger
            $logger = new self($writer);
            $logger->setPluginManager($pm);
            self::$_instances[$pm->getAbbr()][$name] = $logger;
        } else {
            $logger = self::$_instances[$pm->getAbbr()][$name];
            if (!$logger->hasWriter($writer)) {
                $logger->addWriter($writer);
            }
        }

        switch (get_class($writer)) {
            case 'Ifw_Zend_Log_Writer_WpDb':
                $logger->setTimestampFormat('Y-m-d H:i:s');
                break;
        }

        return $logger;
    }

    /**
     * @param IfwZend_Log_FactoryInterface $writer
     * @return bool
     */
    public function hasWriter(IfwZend_Log_FactoryInterface $writer)
    {
        foreach($this->_writers as $w) {
            if (get_class($w) == get_class($writer)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public function setPluginManager(Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
    }

    /**
     * @param string $message
     * @param null $priority
     * @param null $extras
     * @see IfwZend_Log::log()
     */
    public function log($message, $priority=null, $extras=null)
    {
        if ($priority === null) {
            $priority = IfwZend_Log::INFO;
        }
        parent::log($message, $priority, $extras);
    }

    /**
     * @param string $message
     * @param bool $append_backtrace
     * @internal param null $priority
     * @internal param null $extras
     * @see IfwZend_Log::log()
     */
    public function error($message, $append_backtrace = true)
    {
        if ($append_backtrace) {
            $message = $this->_appendBacktrace($message);
        }

        parent::log($message, IfwZend_Log::ERR);
    }

    /**
     * @param string $message
     * @param bool $append_backtrace
     * @internal param null $priority
     * @internal param null $extras
     * @see IfwZend_Log::log()
     */
    public function debug($message, $append_backtrace = true)
    {
        if ($append_backtrace) {
            $message = $this->_appendBacktrace($message);
        }

        parent::log($message, IfwZend_Log::DEBUG);
    }

    /**
     * @param $message
     * @return string
     */
    protected function _appendBacktrace($message)
    {
        $bt = debug_backtrace();

        $format = ' (file: %s, line: %s)';
        return $message . sprintf($format, $bt[1]['file'], $bt[0]['line']);
    }

    /**
     * Only supported by Ifw_Zend_Log_Writer_WpDb
     * @param array $options
     * @internal param int $priority
     */
    public function clear($options = array())
    {
        foreach($this->_writers as $writer) {
            if (get_class($writer) == 'Ifw_Zend_Log_Writer_WpDb') {
                $logs = Ifw_Wp_ORM_Model::factory($writer->getModelName());

                if (isset($options['priority']) && !empty($options['priority'])) {
                    $logs->where_equal('priority', (int)$options['priority']);
                }
                if (isset($options['type']) && !empty($options['type'])) {
                    $logs->where_equal('type', (int)$options['type']);
                }
                $logs->delete_many();
            }
        }
    }

    /**
     * Checks if the table for Ifw_Zend_Log_Writer_WpDb is installed
     * @return bool
     */
    public function isInstalled()
    {
        $result = false;

        foreach($this->_writers as $writer) {
            if (get_class($writer) == 'Ifw_Zend_Log_Writer_WpDb') {
                // install the log table
                global $wpdb, $table_prefix;
                $r = new ReflectionProperty($writer->getModelName(), '_table');
                $query = sprintf('SHOW TABLES LIKE "%s"', $table_prefix . $r->getValue());
                if ($wpdb->get_row($query) !== null) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Installs log writers
     */
    public function install()
    {
        foreach($this->_writers as $writer) {
            if (get_class($writer) == 'Ifw_Zend_Log_Writer_WpDb') {
                // install the log table
                $classname = $writer->getModelName();
                $logModel = new $classname();

                // get the table name of the model using reflection to support PHP 5.2
                $r = new ReflectionProperty($classname, '_table');
                $tableName = $r->getValue();

                $logModel->createTable($tableName);
            }
        }
    }
}

