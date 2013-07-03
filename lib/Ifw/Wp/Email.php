<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * WP Email abstraction
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
class Ifw_Wp_Email
{
    protected $_to;
    protected $_cc;
    protected $_bcc;
    protected $_from;
    protected $_subject;
    protected $_message;
    protected $_attachments = array();
    protected $_headers = array();



    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function addHeader($name, $value)
    {
        $this->_headers[$name] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * @param $name
     * @return string|null
     */
    public function getHeader($name)
    {
        if (isset($this->_headers[$name])) {
            return $this->_headers[$name];
        }
        return null;
    }

    /**
     * @return array
     */
    protected function _getAdjustedHeaders()
    {
        if ($this->getFrom() == null) {
            $this->setFrom(sprintf('%s <%s>', Ifw_Wp_Proxy_Blog::getName(), Ifw_Wp_Proxy_Blog::getAdminEmail()));
        }

        $adjustedHeaders = array();
        foreach($this->getHeaders() as $k => $v) {
            array_push($adjustedHeaders, $k . ':' . $v);
        }
        return $adjustedHeaders;
    }

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->_message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @param $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * @param $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->_to = $to;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->_to;
    }

    /**
     * @param $from
     */
    public function setFrom($from)
    {
        $this->addHeader('from', $from);
        return $this;
    }

    /**
     * @return null|string
     */
    public function getFrom()
    {
        return $this->getHeader('from');
    }

    /**
     * @param $bcc
     * @return $this
     */
    public function setBcc($bcc)
    {
        $this->addHeader('bcc', $bcc);
        return $this;
    }

    /**
     * @return null|string
     */
    public function getBcc()
    {
        return $this->getHeader('bcc');
    }

    /**
     * @param $cc
     * @return $this
     */
    public function setCc($cc)
    {
        $this->addHeader('cc', $cc);
        return $this;
    }

    /**
     * @return null|string
     */
    public function getCc()
    {
        return $this->getHeader('cc');
    }

    /**
     * @param $attachments
     */
    public function setAttachments($attachments)
    {
        $this->_attachments = $attachments;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->_attachments;
    }

    /**
     * @return bool
     */
    public function send()
    {
        return Ifw_Wp_Proxy::mail($this->getTo(), $this->getSubject(), $this->getMessage(), $this->_getAdjustedHeaders(),
            $this->getAttachments());
    }
}
