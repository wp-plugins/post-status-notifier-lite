<?php
$GLOBALS['hook_suffix'] = '';
/**
 * Register Ajax request for metabox 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
$metabox = new Psn_Admin_Metabox_Rules($pm);
return $metabox->getAjaxRequest();
