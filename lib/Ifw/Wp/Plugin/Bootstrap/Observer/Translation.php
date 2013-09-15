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
class Ifw_Wp_Plugin_Bootstrap_Observer_Translation extends Ifw_Wp_Plugin_Bootstrap_Observer_Abstract
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'translation';
    }

    protected function _preBootstrap()
    {
        // load the framework translation
        Ifw_Wp_Proxy::loadTextdomain('ifw', false, $this->_pm->getPathinfo()->getDirname() . '/lib/Ifw/Wp/Translation');

        if (is_dir($this->_pm->getPathinfo()->getRootLang())) {
            $langRelPath = $this->_pm->getPathinfo()->getDirname() . '/lang';
            Ifw_Wp_Proxy::loadTextdomain($this->_pm->getEnv()->getTextDomain(), false, $langRelPath);
        }
    }

}
