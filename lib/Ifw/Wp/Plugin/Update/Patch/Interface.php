<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2012-2013 ifeelweb.de
 * @version   $Id$
 * @package   
 */
interface Ifw_Wp_Plugin_Update_Patch_Interface
{
    /**
     * @param Ifw_Util_Version $presentVersion
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public function execute(Ifw_Util_Version $presentVersion, Ifw_Wp_Plugin_Manager $pm);

    /**
     * @return string
     */
    public function getName();
}
