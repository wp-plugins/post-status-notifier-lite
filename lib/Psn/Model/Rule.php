<?php
/**
 * Rule model
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @copyright   Copyright (c) ifeelweb.de
 * @version     $Id$
 * @package     Psn_Model
 */
class Psn_Model_Rule extends Ifw_Wp_ORM_Model
{
    /**
     * @var string
     */
    public static $_table = 'psn_rules';

    /**
     * @var bool
     */
    protected $_ignoreInherit = false;



    /**
     * @param $orm
     * @return mixed
     */
    public static function active($orm) {
        return $orm->where('active', 1);
    }

    /**
     * @param $subject
     */
    public function setNotificationSubject($subject)
    {
        $this->set('notification_subject', $subject);
    }

    /**
     * @return string
     */
    public function getNotificationSubject()
    {
        return $this->get('notification_subject');
    }

    /**
     * @param $body
     */
    public function setNotificationBody($body)
    {
        $this->set('notification_body', $body);
    }

    /**
     * @return string
     */
    public function getNotificationBody()
    {
        return html_entity_decode($this->get('notification_body'), ENT_COMPAT, Ifw_Wp_Proxy_Blog::getCharset());
    }

    /**
     * @return string
     */
    public function getPostType()
    {
        return $this->get('posttype');
    }

    /**
     * @return string
     */
    public function getStatusBefore()
    {
        return $this->get('status_before');
    }

    /**
     * @return string
     */
    public function getStatusAfter()
    {
        return $this->get('status_after');
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        $categories = $this->get('categories');

        if (!empty($categories)) {
            return unserialize($this->get('categories'));
        }

        return null;
    }

    /**
     * Checks if the rule matches the post's type
     * @param string $postType
     * @return bool
     */
    public function matchesPostType($postType)
    {
        return $this->getPostType() == 'all' or $this->getPostType() == $postType;
    }

    /**
     * Checks if the rule matches the post's status transitions
     * @param string $before
     * @param string $after
     * @return bool
     */
    public function matchesStatus($before, $after)
    {
        return
            ($this->getStatusBefore() == $before or $this->getStatusBefore() == 'anything' or ($this->getStatusBefore() == 'not_published' && $before != 'publish')) &&
            ($this->getStatusAfter() == $after or $this->getStatusAfter() == 'anything' or ($this->getStatusAfter() == 'not_published' && $after != 'publish')) &&
            (!$this->isIgnoreInherit() or ($this->isIgnoreInherit() && $before != 'inherit' && $after != 'inherit'));
    }

    /**
     * @param $post
     * @return bool
     */
    public function matchesCategories($post)
    {
        $categories = $this->getCategories();

        if ($categories === null) {
            // no categories filter set
            return true;
        }

        $postCategories = Ifw_Wp_Proxy_Post::getAllTermIds($post);

        if (isset($categories['include'])) {
            $include = $categories['include'];
        } else {
            // no include set, get all
            $include = Ifw_Wp_Proxy_Post::getAllCategoryIds(Ifw_Wp_Proxy_Post::getType($post));
        }

        $exclude = array();
        if (isset($categories['exclude'])) {
            $exclude = $categories['exclude'];
        }

        // the includes which are not dominated by excludes
        $includeDiff = array_diff($include, $exclude);

        if (count(array_intersect($postCategories, $includeDiff)) > 0) {
            // post has cats that should be included
            return true;
        }

        return false;
    }

    /**
     * @param bool $ignore
     */
    public function setIgnoreInherit($ignore = true)
    {
        if (is_bool($ignore)) {
            $this->_ignoreInherit = $ignore;
        }
    }

    /**
     * @return bool
     */
    public function isIgnoreInherit()
    {
        return $this->_ignoreInherit;
    }

    public static function getMax()
    {
        // please respect my work for this plugin and buy the premium version
        // at http://codecanyon.net/item/post-status-notifier/4809420?ref=ifeelweb
        // otherwise I can not continue updating this plugin with new features
        return Ifw_Wp_Proxy_Filter::apply('psn_max_rules', 2);
    }

    public static function hasMax()
    {
        return self::getMax() > 0;
    }

    public static function reachedMax()
    {
        return Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->count() >= self::getMax();
    }
}
