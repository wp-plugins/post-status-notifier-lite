<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Zend_Navigation_Page_WpMvc extends IfwZend_Navigation_Page_Mvc
{
    protected $_page;
    
    /**
     * Returns href for this page
     *
     * This method uses {@link IfwZend_Controller_Action_Helper_Url} to assemble
     * the href based on the page's properties.
     *
     * @return string  page href
     */
    public function getHref()
    {
        if ($this->_hrefCache) {
            return $this->_hrefCache;
        }

        if (null === self::$_urlHelper) {
            self::$_urlHelper =
                IfwZend_Controller_Action_HelperBroker::getStaticHelper('Url');
        }

        $params = $this->getParams();

        if ($param = $this->getModule()) {
            $params['module'] = $param;
        }

        if ($param = $this->getController()) {
            $params[Ifw_Zend_Controller_Front::getInstance()->getRequest()->getControllerKey()] = $param;
        }

        if ($param = $this->getAction()) {
            $params[Ifw_Zend_Controller_Front::getInstance()->getRequest()->getActionKey()] = $param;
        }
        
        if ($this->_page != null) {
            $params['page'] = $this->_page;
        }

        $url = self::$_urlHelper->url($params,
                                      $this->getRoute(),
                                      $this->getResetParams(),
                                      $this->getEncodeUrl());

        // Add the fragment identifier if it is set
        $fragment = $this->getFragment();       
        if (null !== $fragment) {
            $url .= '#' . $fragment;
        }         

        return $this->_hrefCache = $url;
    }
        
    /**
     * @param field_type $_page
     */
    public function setPage($_page)
    {
        $this->_page = $_page;
    }
}
