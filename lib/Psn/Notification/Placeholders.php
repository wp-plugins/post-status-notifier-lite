<?php
/**
 * This class handles the placeholders replacement
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) 2012-2013 ifeelweb.de
 * @package     Psn_Notification
 */
class Psn_Notification_Placeholders extends Ifw_Util_Replacements
{
    /**
     * The post object the notification is related to
     * @var object|WP_Post
     */
    protected $_post;


    /**
     * @param WP_Post $post
     */
    public function __construct($post = null)
    {
        if ($post === null) {
            $this->_post = $this->_getPostMockup();
        } else {
            // real post object with data
            $this->_post = $post;
        }

        $options = array('auto_delimiters' => true);
        parent::__construct($this->_getNotificationPlaceholders(), $options);
    }

    /**
     * @return mixed|void
     */
    protected function _getNotificationPlaceholders()
    {
        $placeholders = array_merge(
            $this->_getPostData(),
            $this->_getAuthorData(),
            $this->_getCurrentUserData(),
            $this->_getBloginfo()
        );

        return Ifw_Wp_Proxy_Filter::apply('psn_notification_placeholders', $placeholders);
    }

    /**
     * @return array
     */
    protected function _getPostData()
    {
        $result = array();

        foreach (get_object_vars($this->_post) as $k => $v) {
            if (strpos($k, 'post_') === false) {
                $k = 'post_' . $k;
            }
            $result[$k] = $v;
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function _getAuthorData()
    {
        $result = array();

        $whitelist = Ifw_Wp_Proxy_Filter::apply('psn_notification_placeholders_author_data_whitelist',
            array('ID', 'user_login', 'user_email', 'user_url', 'user_registered', 'display_name',
                  'user_firstname', 'user_lastname', 'nickname', 'user_description'));

        if (empty($this->_post->post_author)) {
            // for generating placeholder list on backend help pages (just for the placeholders)
            $userId = Ifw_Wp_Proxy_User::getCurrentUserId();
        } else {
            $userId = (int)$this->_post->post_author;
        }

        $userdata = Ifw_Wp_Proxy_User::getData($userId);

        foreach($whitelist as $prop) {
            if (!$userdata->has_prop($prop)) {
                continue;
            }
            if (strpos($prop, 'user_') === 0) {
                $placeholder = str_replace('user_', 'author_', $prop);
            } else {
                $placeholder = 'author_' . $prop;
            }
            $result[$placeholder] = $userdata->get($prop);
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function _getCurrentUserData()
    {
        $result = array();

        $whitelist = Ifw_Wp_Proxy_Filter::apply('psn_notification_placeholders_current_user_data_whitelist',
            array('ID', 'user_login', 'user_nicename', 'user_email', 'user_url', 'user_registered', 'user_status',
                'display_name', 'user_firstname', 'user_lastname', 'nickname', 'user_description'));

        $userdata = Ifw_Wp_Proxy_User::getCurrentUserData();

        if ($userdata instanceof WP_User) {
            foreach($whitelist as $prop) {
                if (!$userdata->has_prop($prop)) {
                    continue;
                }
                if (strpos($prop, 'user_') === 0) {
                    $placeholder = str_replace('user_', 'current_user_', $prop);
                } else {
                    $placeholder = 'current_user_' . $prop;
                }
                $result[$placeholder] = $userdata->get($prop);
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function _getBloginfo()
    {
        $result = array();

        $whitelist = Ifw_Wp_Proxy_Filter::apply('psn_notification_placeholders_bloginfo_whitelist',
            array('name', 'description', 'wpurl', 'url', 'admin_email', 'version'));

        foreach($whitelist as $v) {
            $result['blog_' . $v] = get_bloginfo($v);
        }

        //$result['blog_admin_display_name'] = Ifw_Wp_Proxy_User::getAdminDisplayName();

        return $result;
    }

    /**
     * @return WP_Post|object
     */
    protected function _getPostMockup()
    {
        if (Ifw_Wp_Proxy_Blog::isMinimumVersion('3.5')) {
            // WP_Post since 3.5
            return new WP_Post(new stdClass());
        } else {
            // before 3.5
            global $wpdb;
            return $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts LIMIT 1"));
        }
    }
}
