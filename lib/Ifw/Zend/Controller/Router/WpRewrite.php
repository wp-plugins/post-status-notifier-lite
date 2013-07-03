<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Overwrite IfwZend_Controller_Router_Rewrite
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Zend_Controller_Router_WpRewrite extends IfwZend_Controller_Router_Rewrite
{
    /**
     * Generates a URL path that can be used in URL creation, redirection, etc.
     *
     * @param  array $userParams Options passed by a user used to override parameters
     * @param  mixed $name The name of a Route to use
     * @param  bool $reset Whether to reset to the route defaults ignoring URL params
     * @param  bool $encode Tells to encode URL parts on output
     * @throws IfwZend_Controller_Router_Exception
     * @return string Resulting absolute URL path
     */
    public function assemble($userParams, $name = null, $reset = false, $encode = true)
    {
        if (!is_array($userParams)) {
            //require_once 'IfwZend/Controller/Router/Exception.php';
            throw new IfwZend_Controller_Router_Exception('userParams must be an array');
        }
        
        if ($name == null) {
            try {
                $name = $this->getCurrentRouteName();
            } catch (IfwZend_Controller_Router_Exception $e) {
                $name = 'default';
            }
        }

        // Use UNION (+) in order to preserve numeric keys
        $params = $userParams + $this->_globalParams;

        $route = $this->getRoute($name);
        $url   = $route->assemble($params, $reset, $encode);

//         if (!preg_match('|^[a-z]+://|', $url)) {
//             $url = rtrim($this->getFrontController()->getBaseUrl(), self::URI_DELIMITER) . self::URI_DELIMITER . $url;
//         }

        return $url;
    }

}
