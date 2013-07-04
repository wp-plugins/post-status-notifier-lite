<?php
/*
Plugin Name: Post Status Notifier Lite
Plugin URI: http://www.ifeelweb.de/wp-plugins/post-status-notifier/
Description: Lets you create individual notification rules to be informed about all post status transitions of your blog. Features custom email texts with many placeholders and custom post types.
Author: ifeelweb.de
Version: 1.0.3
Author URI: http://www.ifeelweb.de
Text Domain: psn
*/

if (!class_exists('Ifw_Wp_Plugin_Loader')) {
    require_once dirname(__FILE__) . '/lib/Ifw/Wp/Plugin/Loader.php';
}

$ifwPluginManager = Ifw_Wp_Plugin_Loader::load(__FILE__)->getPluginManager();
