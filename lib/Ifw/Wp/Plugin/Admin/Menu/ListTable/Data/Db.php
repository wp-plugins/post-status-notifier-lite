<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
abstract class Ifw_Wp_Plugin_Admin_Menu_ListTable_Data_Db implements Ifw_Wp_Plugin_Admin_Menu_ListTable_Data_Interface
{
    /**
     * @var Ifw_Wp_Plugin_Admin_Menu_Model_Mapper_Interface
     */
    protected $_dbMapper;
    
    /**
     * 
     * @param Ifw_Wp_Plugin_Admin_Menu_Model_Mapper_Interface $dbMapper
     */
    public function __construct(Ifw_Wp_Model_Mapper_Interface $dbMapper)
    {
        $this->_dbMapper = $dbMapper;
    }
    
}