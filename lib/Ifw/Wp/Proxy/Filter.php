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
class Ifw_Wp_Proxy_Filter
{
    /**
     * Alias for add_filter
     *
     * @param $tag
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function add($tag, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        return add_filter($tag, $function_to_add, $priority, $accepted_args);
    }

    /**
     * Shortcut for add_filter( 'set-screen-option', 'function_name' )
     *
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addSetScreenOption($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('set-screen-option', $function_to_add, $priority, $accepted_args);
    }

    /**
     * Alias for has_filter
     *
     * @param $tag
     * @param bool $function_to_check
     * @return mixed
     */
    public static function has($tag, $function_to_check = false)
    {
        return has_filter($tag, $function_to_check);
    }

    /**
     * Alias for apply_filters
     *
     * @param $tag
     * @param $value
     * @return mixed|void
     */
    public static function apply($tag, $value)
    {
        return apply_filters($tag, $value);
    }
}
