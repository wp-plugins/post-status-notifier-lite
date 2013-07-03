<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Date format helper class
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2012-2013 ifeelweb.de
 * @version   $Id$
 * @package   
 */ 
class Ifw_Wp_Date
{
    /**
     * Formats a date
     *
     * @param $time expects date format YYYY-MM-DD HH:MM:SS
     * @param $format the output format, blog default will be used if empty
     * @return string the formatted date
     */
    public static function format($time, $format = null)
    {
        $dt = new DateTime($time, new DateTimeZone('UTC'));

        if (empty($format)) {
            $format = Ifw_Wp_Proxy_Blog::getDateFormat() .' '. Ifw_Wp_Proxy_Blog::getTimeFormat();
        }

        $offset = Ifw_Wp_Proxy_Blog::getGmtOffset();
        if (empty($offset)) {
            $offset = 0;
        }
        
        return date($format, $dt->format('U') + ($offset * 3600));
    }

    /**
     * Checks whether a given date string is older than the given seconds
     *
     * @param $time expects date format YYYY-MM-DD HH:MM:SS
     * @param $seconds
     * @return bool
     */
    public static function isOlderThanSeconds($time, $seconds)
    {
        $dt = new DateTime($time, new DateTimeZone('UTC'));

        $offset = Ifw_Wp_Proxy_Blog::getGmtOffset();
        if (empty($offset)) {
            $offset = 0;
        }

        $timeTs = $dt->getTimestamp();

        return $timeTs + $seconds < time();
    }
}
