<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @version   $Id$
 * @package   
 */ 
class IfwPsn_Wp_WunderScript_Extension_Globals implements IfwPsn_Wp_WunderScript_Extension_Interface
{
    public function load(IfwPsn_Vendor_Twig_Environment $env)
    {
        require_once dirname(__FILE__) . '/Global/Wp.php';

        $env->addGlobal( 'wp', new IfwPsn_Wp_WunderScript_Extension_Global_Wp() );
    }
}
 