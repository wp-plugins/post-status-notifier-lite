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
class Ifw_Wp_ORM_Model extends Ifw_Wp_ORM_ModelParis
{
    /**
     * Checks if table exists
     * @return bool
     */
    public function exists()
    {
        global $wpdb, $table_prefix;
        $result = false;

        $r = new ReflectionProperty($this, '_table');
        $query = sprintf('SHOW TABLES LIKE "%s"', $table_prefix . $r->getValue());
        if ($wpdb->get_row($query) !== null) {
            $result = true;
        }

        return $result;
    }
}
