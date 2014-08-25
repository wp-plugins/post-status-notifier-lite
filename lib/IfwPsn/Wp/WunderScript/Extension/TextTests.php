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
class IfwPsn_Wp_WunderScript_Extension_TextTests implements IfwPsn_Wp_WunderScript_Extension_Interface
{
    public function load(IfwPsn_Vendor_Twig_Environment $env)
    {
        require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Twig/SimpleTest.php';

        $env->addTest( new IfwPsn_Vendor_Twig_SimpleTest('serialized', 'is_serialized') );
    }
}
 