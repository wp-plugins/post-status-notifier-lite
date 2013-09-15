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
class Psn_Bootstrap_Observer_MenuPage extends Ifw_Wp_Plugin_Bootstrap_Observer_Abstract
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'menu_page';
    }

    protected function _preBootstrap()
    {
        $optionsPage = new Psn_Menu_Options($this->_pm);

        $optionsPage
            ->setMenuTitle($this->_pm->getEnv()->getName())
            ->setSlug($this->_pm->getPathinfo()->getDirname())
            ->init()
        ;

        $this->_resource = $optionsPage;
    }
}
