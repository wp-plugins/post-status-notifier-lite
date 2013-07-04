<?php
/**
 *
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) 2012-2013 ifeelweb.de
 * @package     Psn_Admin
 */
class Psn_Admin_ListTable_Data_Rules implements Ifw_Wp_Plugin_Admin_Menu_ListTable_Data_Interface
{

    /** (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_ListTable_Data_Interface::getItems()
     */
    public function getItems($limit, $page, $order = null, $where = null)
    {
        $offset = ($page-1) * $limit;
        if (empty($order)) {
            $order = array('name' => 'asc');
        }

        $data = Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->limit($limit)->offset($offset);

        if (!empty($order)) {
            $orderBy = key($order);
            $orderDir = $order[$orderBy];
            if ($orderDir == 'desc') {
                $data->order_by_desc($orderBy);
            } else {
                $data->order_by_asc($orderBy);
            }
        }

        if (!empty($where)) {
            $data->where_like('name', '%' . $where . '%');
        }

        $result = $data->find_array();

        if (Psn_Model_Rule::hasMax()) {
            $result = array_slice($result, 0, Psn_Model_Rule::getMax());
        }

        return $result;
    }

    /** (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_ListTable_Data_Interface::getTotalItems()
     */
    public function getTotalItems()
    {
        $result = Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->count();

        if (Psn_Model_Rule::hasMax()) {
            $result = Psn_Model_Rule::getMax();
        }

        return $result;
    }

}
