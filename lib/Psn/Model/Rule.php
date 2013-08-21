<?php
/**
 * Rule model
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @copyright   Copyright (c) 2012-2013 ifeelweb.de
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

    public function getPostType()
    {
        return $this->get('posttype');
    }

    public function getStatusBefore()
    {
        return $this->get('status_before');
    }

    public function getStatusAfter()
    {
        return $this->get('status_after');
    }

    public function matchesPostType($postType)
    {
        return $this->getPostType() == 'all' or $this->getPostType() == $postType;
    }

    public function matchesStatus($before, $after)
    {
        return
            ($this->getStatusBefore() == $before or $this->getStatusBefore() == 'anything') &&
            ($this->getStatusAfter() == $after or $this->getStatusAfter() == 'anything') &&
            (!$this->isIgnoreInherit() or ($this->isIgnoreInherit() && $before != 'inherit' && $after != 'inherit'));
    }

    public static function getMax()
    {
        // please respect my work for this plugin and buy the premium version
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

}
