<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2012-2013 ifeelweb.de
 * @version   $Id$
 * @package   
 */ 
class Ifw_Wp_Proxy_Db 
{
    /**
     * Convenience method to get code completion in IDE
     * @return wpdb
     */
    public static function getObject()
    {
        global $wpdb;
        return $wpdb;
    }

    /**
     * Retrieves the database name
     * @return string
     */
    public static function getName()
    {
        return DB_NAME;
    }

    /**
     * @return string
     */
    public static function getPrefix()
    {
        global $table_prefix;
        return $table_prefix;
    }

    /**
     * Get the table name with prefix
     * @param $table
     * @return string
     */
    public static function getTableName($table)
    {
        if (strpos($table, self::getPrefix()) !== 0) {
            return self::getPrefix() . $table;
        }

        return $table;
    }

    /**
     * Checks if a column in a table exists
     * @param $table
     * @param $column
     * @return bool
     */
    public static function columnExists($table, $column)
    {
        $sql = 'SELECT count(*)
            FROM information_schema.COLUMNS
            WHERE
                TABLE_SCHEMA = "%s"
            AND TABLE_NAME = "%s"
            AND COLUMN_NAME = "%s"
        ';

        $sql = sprintf($sql, self::getName(), self::getPrefix() . $table, $column);

        return (int)self::getObject()->get_var($sql) === 1;
    }
}
