<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Tries to reset the options set by the plugin
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
class Ifw_Wp_Plugin_Installer_Command_UninstallResetOptions implements Ifw_Wp_Plugin_Installer_UninstallInterface
{
    /**
     * @param Ifw_Wp_Plugin_Manager|null $pm
     * @return mixed|void
     */
    public static function execute($pm)
    {
        if (!($pm instanceof Ifw_Wp_Plugin_Manager)) {
            return;
        }

        if (Ifw_Wp_Proxy_Blog::isMultisite()) {

            // multisite installation
            $currentBlogId = Ifw_Wp_Proxy_Blog::getBlogId();

            foreach (Ifw_Wp_Proxy_Blog::getMultisiteBlogIds() as $blogId) {

                Ifw_Wp_Proxy_Blog::switchToBlog($blogId);
                $pm->getOptions()->reset();
            }
            Ifw_Wp_Proxy_Blog::switchToBlog($currentBlogId);

        } else {
            // single blog installation
            $pm->getOptions()->reset();
        }

    }
}
