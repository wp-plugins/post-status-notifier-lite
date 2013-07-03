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
class Ifw_Wp_Proxy_Post extends Ifw_Wp_Proxy_Abstract
{
    /**
     * @return array
     */
    public static function getAllStatusKeys()
    {
        global $wp_post_statuses;
        return array_keys($wp_post_statuses);
    }

    /**
     * @return array
     */
    public static function getAllTypesKeys()
    {
        return array_unique(
            array_merge(
                array_keys(get_post_types()),
                array_keys(get_post_types(array('_builtin' => false)))
            )
        );
    }

    /**
     * @return array
     */
    public static function getAllTypesWithLabels()
    {
        $types = array();

        foreach(get_post_types(array(), 'objects') as $type => $values) {
            $types[$type] = $values->labels->name;
        }
        foreach(get_post_types(array('_builtin' => false), 'objects') as $type => $values) {
            $types[$type] = $values->labels->name;
        }

        return array_unique($types);
    }

    /**
     * @param $type
     * @return mixed
     */
    public static function getTypeLabel($type)
    {
        $types = self::getAllTypesWithLabels();

        if (isset($types[$type])) {
            $label = $types[$type];
        } else {
            $label = $type;
        }

        return $label;
    }

    /**
     * @return array
     */
    public static function getAllStatusesWithLabels()
    {
        $statuses = array();
        foreach(self::getAllStatusKeys() as $status) {
            $statuses[$status] = self::getStatusLabel($status);
        }
        return $statuses;
    }

    /**
     * @param $status
     * @return string|void
     */
    public static function getStatusLabel($status)
    {
        global $wp_post_statuses;

        if (isset($wp_post_statuses[$status])) {
            $label = $wp_post_statuses[$status]->label;
        } else {
            $label = $status;
        }
        return $label;
    }

    /**
     * @param $postId
     * @return mixed
     */
    public static function getSlug($postId)
    {
        $post_data = get_post($postId, ARRAY_A);
        $slug = $post_data['post_name'];
        return $slug;
    }
}
