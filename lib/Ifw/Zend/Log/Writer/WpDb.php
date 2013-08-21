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
class Ifw_Zend_Log_Writer_WpDb extends IfwZend_Log_Writer_Abstract
{
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var
     */
    protected $_modelName;


    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     * @param $modelName
     */
    public function __construct(Ifw_Wp_Plugin_Manager $pm, $modelName)
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
                $dataToInsert[$item] = htmlentities($event[$item], ENT_COMPAT, Ifw_Wp_Proxy_Blog::getCharset());
            }
        }

        return Ifw_Wp_ORM_Model::factory($this->_modelName)->create($dataToInsert)->save();
    }

    /**
     * Construct a IfwZend_Log driver
     *
     * @param  array|IfwZend_Config $config
     * @return IfwZend_Log_FactoryInterface
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
