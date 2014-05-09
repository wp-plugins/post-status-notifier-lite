<?php
/**
 * Rule model
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @copyright   Copyright (c) ifeelweb.de
 * @version     $Id$
 * @package     Psn_Model
 */
class Psn_Model_Rule extends IfwPsn_Wp_ORM_Model
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
     * @var null|array
     */
    protected $_recipient;

    /**
     * @var null|array
     */
    protected $_ccSelect;

    /**
     * @var null|array
     */
    protected $_bccSelect;

    /**
     * @var null|array
     */
    protected $_editorRestriction;



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
        return html_entity_decode($this->get('notification_body'), ENT_COMPAT, IfwPsn_Wp_Proxy_Blog::getCharset());
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
            return unserialize($categories);
        }

        return null;
    }

    /**
     * @return array
     */
    public function getRecipient()
    {
        if ($this->_recipient === null) {

            $this->_recipient = array();

            $value = $this->get('recipient');

            if (!empty($value)) {

                $value = @unserialize($value);

                if ($value !== false) {
                    // unserialize worked
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                    $this->_recipient = $value;

                } else {
                    // for backwards compat put string in array to work on multiselect
                    $this->_recipient = array($this->get('recipient'));
                }
            }
        }

        return $this->_recipient;
    }

    /**
     * @return array
     */
    public function getCcSelect()
    {
        if ($this->_ccSelect === null) {

            $this->_ccSelect = array();

            $value = $this->get('cc_select');

            if (!empty($value)) {

                $value = @unserialize($value);

                if ($value !== false) {
                    // unserialize worked
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                    $this->_ccSelect = $value;
                }
            }
        }

        return $this->_ccSelect;
    }

    /**
     * @return array
     */
    public function getBccSelect()
    {
        if ($this->_bccSelect === null) {

            $this->_bccSelect = array();

            $value = $this->get('bcc_select');

            if (!empty($value)) {

                $value = @unserialize($value);

                if ($value !== false) {
                    // unserialize worked
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                    $this->_bccSelect = $value;
                }
            }
        }

        return $this->_bccSelect;
    }

    /**
     * @return array
     */
    public function getEditorRestriction()
    {
        if ($this->_editorRestriction === null) {

            $this->_editorRestriction = array();

            $value = $this->get('editor_restriction');

            if (!empty($value)) {

                $value = @unserialize($value);

                if ($value !== false) {
                    // unserialize worked
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                    $this->_editorRestriction = $value;
                }
            }
        }

        return $this->_editorRestriction;
    }

    /**
     * The main match method. Determines a post status transition matches a notification rule's settings
     *
     * @param $post
     * @param $before
     * @param $after
     * @return bool
     */
    public function matches($post, $before, $after)
    {
        return
            $this->matchesPostType($post->post_type) and
            $this->matchesStatus($before, $after) and
            $this->matchesCategories($post) and
            $this->matchesSpecialCases($post, $before, $after)
        ;
    }

    /**
     * Checks if the rule matches the post's type
     *
     * @param string $postType
     * @return bool
     */
    public function matchesPostType($postType)
    {
        return $this->getPostType() == 'all' or $this->getPostType() == $postType;
    }

    /**
     * Checks if the rule matches the post's status transitions
     *
     * @param string $before
     * @param string $after
     * @return bool
     */
    public function matchesStatus($before, $after)
    {
        return
            $this->_matchesBeforeStatus($before) and
            $this->_matchesAfterStatus($after)
            ;
    }

    /**
     * Checks if before status matches
     *
     * @param $before
     * @return bool
     */
    protected function _matchesBeforeStatus($before)
    {
        if (
            // exact match:
            $this->getStatusBefore() == $before or

            // "anything" matches always:
            $this->getStatusBefore() == 'anything' or

            // "not_published" validation:
            ($this->getStatusBefore() == 'not_published' && $before != 'publish') or

            // "not_private" validation:
            ($this->getStatusBefore() == 'not_private' && $before != 'private') or

            // "not_pending" validation:
            ($this->getStatusBefore() == 'not_pending' && $before != 'pending')

            ) {

            return true;
        }

        return false;
    }

    /**
     * Checks if after status matches
     *
     * @param $after
     * @return bool
     */
    protected function _matchesAfterStatus($after)
    {
        if (
            // exact match:
            $this->getStatusAfter() == $after or

            // "anything" matches always:
            $this->getStatusAfter() == 'anything' or

            // "not_published" validation:
            ($this->getStatusAfter() == 'not_published' && $after != 'publish') or

            // "not_private" validation:
            ($this->getStatusAfter() == 'not_private' && $after != 'private') or

            // "not_pending" validation:
            ($this->getStatusAfter() == 'not_pending' && $after != 'pending')

            ) {

            return true;
        }

        return false;
    }

    /**
     * Checks for special matching cases
     *
     * @param $post
     * @param $before
     * @param $after
     * @return bool
     */
    public function matchesSpecialCases($post, $before, $after)
    {
        return
            $this->_matchesInheritanceSettings($before, $after) and
            $this->_matchesEditorRestriction()
            ;
    }

    /**
     * Checks if inheritance settings match
     *
     * @param $before
     * @param $after
     * @return bool
     */
    protected function _matchesInheritanceSettings($before, $after)
    {
        if (
            $this->isIgnoreInherit() === false or
            ($this->isIgnoreInherit() === true && $before != 'inherit' && $after != 'inherit') ) {

            return true;
        }

        return false;
    }

    /**
     * Checks if editor restriction matches
     *
     * @return bool
     */
    protected function _matchesEditorRestriction()
    {
        $editorRestriction = $this->getEditorRestriction();

        if (
            // no restriction set:
            empty($editorRestriction) or
            // determine if user is member of restricted roles:
            IfwPsn_Wp_Proxy_User::isCurrentUserMemberOfRoles($editorRestriction)

            ) {

            return true;
        }

        return false;
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

        $postCategories = IfwPsn_Wp_Proxy_Post::getAllTermIds($post);

        if (isset($categories['include'])) {
            $include = $categories['include'];
        } else {
            // no include set, get all
            $include = IfwPsn_Wp_Proxy_Post::getAllCategoryIds(IfwPsn_Wp_Proxy_Post::getType($post));
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
        return IfwPsn_Wp_Proxy_Filter::apply('psn_max_rules', 2);
    }

    public static function hasMax()
    {
        return self::getMax() > 0;
    }

    public static function reachedMax()
    {
        return IfwPsn_Wp_ORM_Model::factory('Psn_Model_Rule')->count() >= self::getMax();
    }
}
