<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 *
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
class IfwPsn_Wp_Http_Request
{
    /**
     * @var IfwPsn_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var string
     */
    protected $_url;

    /**
     * @var string
     */
    protected $_userAgent;

    /**
     * @var array
     */
    protected $_data = array();



    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     */
    public function __construct(IfwPsn_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;

        $this->_init();
    }

    /**
     *
     */
    protected function _init()
    {
        $this->setUserAgent('WordPress/' . IfwPsn_Wp_Proxy_Blog::getVersion() . '; ' . IfwPsn_Wp_Proxy_Blog::getUrl());
        $this->addData('referrer', IfwPsn_Wp_Proxy_Blog::getUrl());
        $this->addData('browser_user_agent', $_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * @return IfwPsn_Wp_Http_Response
     */
    public function send()
    {
        $args = array(
            'body' => $this->getData(),
            'user-agent' => $this->getUserAgent()
        );

        $response = wp_remote_post($this->getUrl(), $args);

        return new IfwPsn_Wp_Http_Response($response);
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param string $userAgent
     * @return $this
     */
    public function setUserAgent($userAgent)
    {
        $this->_userAgent = $userAgent;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->_userAgent;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function addData($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }
}
 