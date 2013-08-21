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
class Ifw_Wp_Plugin_Logger_Model extends Ifw_Wp_ORM_Model
{
    /**
     * @var array
     */
    public static $eventItems = array(
        'priority',
        'message',
        'type',
        'timestamp',
        'extra'
    );

    public function createTable($tablename)
    {
        global $wpdb, $table_prefix;

        $query = '
        CREATE TABLE IF NOT EXISTS `%s` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `priority` int(11) NOT NULL,
          `message` varchar(255) CHARACTER SET utf8 NOT NULL,
          `type` smallint(4) NOT NULL,
          `timestamp` datetime NOT NULL,
          `extra` text COLLATE utf8_unicode_ci NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ';

        return $wpdb->query(sprintf($query, $table_prefix . $tablename));
    }
}
