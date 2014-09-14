<?php
/**
 *
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2014 ifeelweb.de
 * @version   $Id$
 * @package
 */

class Psn_Notification_Deferred_Handler 
{
    /**
     * @var array
     */
    private $_container = array();


    public function __construct()
    {
        // register the execute method to wp_insert_post action
        // to execute notification services after the post got saved completely
        // (including custom fields managed by plugins etc.)
        IfwPsn_Wp_Proxy_Action::addWpInsertPost(array($this, 'execute'), 1000000);
    }

    /**
     * Gets executed as last action after a post update (on action "wp_insert_post")
     *
     * @param $post_ID
     * @param $post
     * @param $update
     */
    public function execute($post_ID, $post, $update)
    {
        /**
         * @var Psn_Notification_Deferred_Container $container
         */
        foreach ($this->_container as $container) {
            if ($container->matchesPost($post)) {
                $container->execute($post);
            }
        }
    }

    /**
     * @param Psn_Notification_Deferred_Container $container
     */
    public function addCotainer(Psn_Notification_Deferred_Container $container)
    {
        array_push($this->_container, $container);
    }
}
 