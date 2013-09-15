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
class Ifw_Wp_Plugin_Installer_Command_ActivationPresentVersion implements Ifw_Wp_Plugin_Installer_ActivationInterface
{
    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     * @param $networkwide
     * @return mixed
     */
    public function execute(Ifw_Wp_Plugin_Manager $pm, $networkwide = false)
    {
        if (Ifw_Wp_Proxy_Blog::isMultisite() && $networkwide == true) {

            // multisite installation
            $currentBlogId = Ifw_Wp_Proxy_Blog::getBlogId();

            foreach (Ifw_Wp_Proxy_Blog::getMultisiteBlogIds() as $blogId) {

                Ifw_Wp_Proxy_Blog::switchToBlog($blogId);
                $this->_refreshPresentVersion($pm);
            }
            Ifw_Wp_Proxy_Blog::switchToBlog($currentBlogId);

        } else {
            // single blog installation
            $this->_refreshPresentVersion($pm);
        }
    }

    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    protected function _refreshPresentVersion(Ifw_Wp_Plugin_Manager $pm)
    {
        $pm->getBootstrap()->getUpdateManager()->refreshPresentVersion();
    }
}
