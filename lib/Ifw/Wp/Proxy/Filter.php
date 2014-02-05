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
     * Alias for add_filter
     *
     * @param Ifw_Wp_Plugin_Manager $pm
     * @param $tag
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addPlugin(Ifw_Wp_Plugin_Manager $pm, $tag, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        $tag = $pm->getAbbrLower() . '_' . $tag;
        return self::add($tag, $function_to_add, $priority, $accepted_args);
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
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addTheExcerpt($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('the_excerpt', $function_to_add, $priority, $accepted_args);
    }

    /**
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addTheExcerptFeed($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('the_excerpt_feed', $function_to_add, $priority, $accepted_args);
    }

    /**
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addTheExcerptRss($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('the_excerpt_rss', $function_to_add, $priority, $accepted_args);
    }

    /**
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addTheContentFeed($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('the_content_feed', $function_to_add, $priority, $accepted_args);
    }

    /**
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addWidgetText($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('widget_text', $function_to_add, $priority, $accepted_args);
    }

    /**
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addTheContentRss($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('the_content_rss', $function_to_add, $priority, $accepted_args);
    }

    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     * @param $function_to_add
     * @param int $priority
     * @internal param int $accepted_args
     * @return bool|void
     */
    public static function addPluginActionLinks(Ifw_Wp_Plugin_Manager $pm, $function_to_add, $priority = 10)
    {
        return self::add('plugin_action_links_'. $pm->getPathinfo()->getFilenamePath(), $function_to_add, $priority, 2);
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
