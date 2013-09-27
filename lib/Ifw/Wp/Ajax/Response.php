<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Ajax response
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Wp_Ajax_Response
{
    /**
     * @var bool
     */
    protected $_success;
    
    /**
     * @var string
     */
    protected $_html;

    /**
     * @var array
     */
    protected $_extra = array();


    /**
     *
     * @param bool $success
     * @param $html
     */
    public function __construct ($success, $html)
    {
        $this->setSuccess($success);
        $this->setHtml($html);
    }
    
    /**
     * @return bool
     */
    public function getSuccess()
    {
        return $this->_success;
    }

    /**
     * @param $success
     */
    public function setSuccess($success)
    {
        if (is_bool($success)) {
            $this->_success = $success;
        }
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->_html;
    }

    /**
     * @param $html
     */
    public function setHtml($html)
    {
        $this->_html = $html;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addExtra($key, $value)
    {
        $this->_extra[$key] = $value;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->_extra;
    }

}
