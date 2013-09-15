<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Set default form translator
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
class Ifw_Wp_Plugin_Application_Adapter_ZendFw_Autostart_ZendFormTranslation extends Ifw_Wp_Plugin_Application_Adapter_ZendFw_Autostart_Abstract
{
    public function execute()
    {
        $translator = new IfwZend_Translate(
            'array',
            $this->_adapter->getPluginManager()->getPathinfo()->getRootLib() . 'Ifw/Zend/Form/resources/languages',
            Ifw_Wp_Proxy_Blog::getLanguage(),
            array('scan' => IfwZend_Translate::LOCALE_DIRECTORY)
        );

        IfwZend_Validate_Abstract::setDefaultTranslator($translator);
    }
}
