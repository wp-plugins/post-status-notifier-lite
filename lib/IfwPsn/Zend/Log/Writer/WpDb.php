<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Log/Writer/Abstract.php';

class IfwPsn_Zend_Log_Writer_WpDb extends IfwPsn_Vendor_Zend_Log_Writer_Abstract
{
    /**
     * @var IfwPsn_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var
     */
    protected $_modelName;


    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     * @param $modelName
     */
    public function __construct(IfwPsn_Wp_Plugin_Manager $pm, $modelName)
    {
        $this->_pm = $pm;
        $this->setModelName($modelName);
    }

    /**
     * Write a message to the log.
     *
     * @param  array $event  log data event
     * @return bool
     */
    protected function _write($event)
    {
        $dataToInsert = array();
        $r = new ReflectionProperty($this->_modelName, 'eventItems');

        foreach ($r->getValue() as $item) {
            if (isset($event[$item])) {
                if (is_array($event[$item]) || is_object($event[$item])) {
                    $dataToInsert[$item] = var_export($dataToInsert[$item], true);
                } else {
                    $dataToInsert[$item] = htmlentities(utf8_encode($event[$item]), ENT_COMPAT, IfwPsn_Wp_Proxy_Blog::getCharset());
                }
            }
        }

        return IfwPsn_Wp_ORM_Model::factory($this->_modelName)->create($dataToInsert)->save();
    }

    /**
     * Construct a IfwPsn_Vendor_Zend_Log driver
     *
     * @param  array|IfwPsn_Vendor_Zend_Config $config
     * @return IfwPsn_Vendor_Zend_Log_FactoryInterface
     */
    static public function factory($config)
    {
        // not supported
    }

    /**
     * @param  $modelName
     */
    public function setModelName($modelName)
    {
        $this->_modelName = $modelName;
    }

    /**
     * @return
     */
    public function getModelName()
    {
        return $this->_modelName;
    }
}
