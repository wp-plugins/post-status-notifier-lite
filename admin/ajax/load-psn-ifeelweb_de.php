<?php
/**
 * Register Ajax request for metabox 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
$metabox = new Ifw_Wp_Plugin_Admin_Menu_Metabox_IfwFeed($pm);
return $metabox->getAjaxRequest();
