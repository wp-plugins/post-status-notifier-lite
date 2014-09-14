<?php
/**
 * Register Ajax request for metabox 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
$metabox = new IfwPsn_Wp_Plugin_Metabox_IfwFeed($pm);
return $metabox->getAjaxRequest();
