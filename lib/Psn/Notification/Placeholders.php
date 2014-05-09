<?php
/**
 * This class handles the placeholders replacement
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) ifeelweb.de
 * @package     Psn_Notification
 */
class Psn_Notification_Placeholders extends IfwPsn_Util_Replacements
{
    /**
     * The post object the notification is related to
     * @var object|WP_Post
     */
    protected $_post;

    /**
     * @var bool
     */
    protected $_isMockUpPost = false;



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

        $this->_addDynamicPlaceholders();
    }

    /**
     *
     */
    protected function _addDynamicPlaceholders()
    {
        $dynamicPlaceholders = array();

        $group = 'dynamic';

        foreach (IfwPsn_Wp_Proxy_Taxonomy::getPublicCategoriesNames() as $category) {
            $dynamicPlaceholders['post_category-' . $category] = implode(', ', IfwPsn_Wp_Proxy_Post::getAttachedCategoriesNames($this->_post, $category));
        }
        foreach (IfwPsn_Wp_Proxy_Taxonomy::getPublicTagsNames() as $tag) {
            if ($tag == 'post_format') {
                continue;
            }
            $dynamicPlaceholders['post_tag-' . $tag] = implode(', ', IfwPsn_Wp_Proxy_Post::getAttachedTagsNames($this->_post, $tag));
        }

        // custom keys
        if (!$this->isMockUpPost()) {
            foreach (IfwPsn_Wp_Proxy_Post::getCustomKeys($this->_post) as $key) {
                $dynamicPlaceholders['post_custom_field-' . $key] = IfwPsn_Wp_Proxy_Post::getCustomKeyValue($key, $this->_post);
            }
        }

        foreach (IfwPsn_Wp_Proxy_Filter::apply('psn_notification_dynamic_placeholders', $dynamicPlaceholders) as $key => $value) {
            $this->addPlaceholder($key, $value, $group);
        }
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

        return IfwPsn_Wp_Proxy_Filter::apply('psn_notification_placeholders', $placeholders);
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

        $result['post_permalink'] = IfwPsn_Wp_Proxy_Post::getPermalink($this->_post);
        $result['post_editlink'] = IfwPsn_Wp_Proxy_Post::getEditLink($this->_post->ID);
        $result['post_format'] = IfwPsn_Wp_Proxy_Post::getFormat($this->_post);

        // get the post's categories
        $result['post_categories'] = implode(', ', IfwPsn_Wp_Proxy_Post::getAttachedCategoriesNames($this->_post));

        // get the post's tags
        $result['post_tags'] = implode(', ', IfwPsn_Wp_Proxy_Post::getAttachedTagsNames($this->_post));

        // custom keys
        $customKeys = IfwPsn_Wp_Proxy_Post::getCustomKeys($this->_post);
        $result['post_custom_fields'] = implode(', ', $customKeys);


        // custom keys and values
        $custom_keys_and_values = array();
        foreach (IfwPsn_Wp_Proxy_Post::getCustomKeysAndValues($this->_post) as $key => $value) {
            array_push($custom_keys_and_values, $key . ': ' . $value);
        }
        $result['post_custom_fields_and_values'] = implode(', ', $custom_keys_and_values);

        return $result;
    }

    /**
     * @return array
     */
    protected function _getAuthorData()
    {
        $result = array();

        $whitelist = IfwPsn_Wp_Proxy_Filter::apply('psn_notification_placeholders_author_data_whitelist',
            array('ID', 'user_login', 'user_email', 'user_url', 'user_registered', 'display_name',
                  'user_firstname', 'user_lastname', 'nickname', 'user_description'));

        if (empty($this->_post->post_author)) {
            // for generating placeholder list on backend help pages (just for the placeholders)
            $userId = IfwPsn_Wp_Proxy_User::getCurrentUserId();
        } else {
            $userId = (int)$this->_post->post_author;
        }

        $userdata = IfwPsn_Wp_Proxy_User::getData($userId);

        if ($userdata instanceof WP_User) {
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
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function _getCurrentUserData()
    {
        $result = array();

        $whitelist = IfwPsn_Wp_Proxy_Filter::apply('psn_notification_placeholders_current_user_data_whitelist',
            array('ID', 'user_login', 'user_nicename', 'user_email', 'user_url', 'user_registered', 'user_status',
                'display_name', 'user_firstname', 'user_lastname', 'nickname', 'user_description'));

        $userdata = IfwPsn_Wp_Proxy_User::getCurrentUserData();

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

        $whitelist = IfwPsn_Wp_Proxy_Filter::apply('psn_notification_placeholders_bloginfo_whitelist',
            array('name', 'description', 'wpurl', 'url', 'admin_email', 'version'));

        foreach($whitelist as $v) {
            $result['blog_' . $v] = get_bloginfo($v);
        }

        //$result['blog_admin_display_name'] = IfwPsn_Wp_Proxy_User::getAdminDisplayName();

        return $result;
    }

    /**
     * @return WP_Post|object
     */
    protected function _getPostMockup()
    {
        $this->_isMockUpPost = true;

        if (IfwPsn_Wp_Proxy_Blog::isMinimumVersion('3.5')) {
            // WP_Post since 3.5
            return new WP_Post(new stdClass());
        } else {
            // before 3.5
            global $wpdb;
            return $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts LIMIT 1"));
        }
    }

    /**
     * @return bool
     */
    public function isMockUpPost()
    {
        return $this->_isMockUpPost === true;
    }

    /**
     * @return string
     */
    public function getOnScreenHelp()
    {
        $tpl = IfwPsn_Wp_Tpl::getInstance(IfwPsn_Wp_Plugin_Manager::getInstance('Psn'));

        $this->addPlaceholder('post_status_before')->addPlaceholder('post_status_after');

        $placholdersResult = $this->getDefaultPlaceholders();
        asort($placholdersResult);
        $placholdersDynamic = $this->getPlaceholders('dynamic');
        asort($placholdersDynamic);

        $context = array(
            'placeholders' => $placholdersResult,
            'placeholdersDynamic' => $placholdersDynamic,
            'placeholdersDynamicHelp' => __('These placeholders are unique to this WordPress installation. They use the names of custom categories and tags.', 'psn'),
            'langHeader' => __('List of placeholders available for notification subject and text', 'psn'),
            'langStatic' => __('Static placeholders', 'psn'),
            'langDynamic' => __('Dynamic placeholders', 'psn'),
            'langCustomFields' => __('Custom fields', 'psn'),
            'langCustomFields1' => __('To retrieve the contents of custom post fields use this placeholder', 'psn'),
            'langCustomFields2' => __('The * stands for the name of the custom field.<br>Example: If you have a custom post field "actors" you should call your placeholder <b>[post_custom_field-actors]</b>', 'psn'),
        );

        return $tpl->render('admin_help_placeholders.html.twig', $context);
    }
}
