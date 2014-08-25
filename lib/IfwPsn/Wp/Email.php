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
class IfwPsn_Wp_Email
{
    protected $_uniqueId;
    protected $_to;
    protected $_cc;
    protected $_bcc;
    protected $_from;
    protected $_subject;
    protected $_message;
    protected $_altbody;
    protected $_attachments = array();
    protected $_headers = array();
    protected $_isHTML = false;

    /**
     * If true, sends one mail per To disregarding Cc and Bcc
     * @var bool
     */
    protected $_sendLoopTo = false;

    protected $_time_limit;




    public function __construct()
    {
        $this->_uniqueId = uniqid('ifw_email_');
    }

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
     * @param $name
     */
    public function unsetHeader($name)
    {
        if (isset($this->_headers[$name])) {
            unset($this->_headers[$name]);
        }
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
            //$this->setFrom(sprintf('%s <%s>', $this->_getFilteredBlogName(), IfwPsn_Wp_Proxy_Blog::getAdminEmail()));
        }

        $adjustedHeaders = array();
        foreach($this->getHeaders() as $k => $v) {
            array_push($adjustedHeaders, $k . ':' . $v);
        }
        return $adjustedHeaders;
    }

    /**
     * @return string
     */
    protected function _getFilteredBlogName()
    {
        return strtr(IfwPsn_Wp_Proxy_Blog::getName(), array(
            '&#039;' => '\'',
        ));
    }

    /**
     * @return mixed
     */
    public function getUniqueId()
    {
        return $this->_uniqueId;
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
     * Alias for setMessage
     * @param $message
     * @return $this
     */
    public function setBody($message)
    {
        $this->setMessage($message);
        return $this;
    }

    /**
     * Alias for getMessage
     * @return mixed
     */
    public function getBody()
    {
        return $this->getMessage();
    }

    /**
     * @param mixed $altbody
     */
    public function setAltbody($altbody)
    {
        $this->_altbody = $altbody;
    }

    /**
     * @return mixed
     */
    public function getAltbody()
    {
        return $this->_altbody;
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

    public function unsetTo()
    {
        $this->_to = null;
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
     * @return $this
     */
    public function setFrom($from)
    {
        $this->addHeader('from', $from);
        return $this;
    }

    public function unsetFrom()
    {
        $this->unsetHeader('from');
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
     * @return $this
     */
    public function unsetBcc()
    {
        $this->unsetHeader('bcc');
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
     * @return $this
     */
    public function unsetCc()
    {
        $this->unsetHeader('cc');
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
     * @param bool $set
     * @return $this
     */
    public function setLoopTo($set = true)
    {
        if (is_bool($set)) {
            $this->_sendLoopTo = $set;
        }
        return $this;
    }

    /**
     * Determines if loopTo switch is on
     * @return bool
     */
    public function isLoopTo()
    {
        return $this->_sendLoopTo === true;
    }

    /**
     * @param $secs
     */
    public function setTimelimit($secs)
    {
        if (is_int($secs)) {
            $this->_time_limit = $secs;
        }
    }

    /**
     * @return bool
     */
    public function send()
    {
        if ($this->isLoopTo()) {
            $result = $this->_sendLoopTo();
        } else {
            $result = $this->_sendDefault();
        }

        return $result;
    }

    /**
     * Default send procedure. One mail for all, including CC and BCC
     * @return bool
     */
    protected function _sendDefault()
    {
        return IfwPsn_Wp_Proxy::mail($this->getTo(), $this->getSubject(), $this->getMessage(), $this->_getAdjustedHeaders(), $this->getAttachments());
    }

    /**
     * TO Loop
     * Sends a single mail to each TO's
     */
    protected function _sendLoopTo()
    {
        $result = true;

        $this->unsetCc()->unsetBcc();

        $toStack = explode(',', $this->getTo());

        // adjust the time limit on demand
        if (is_int($this->_time_limit)) {
            set_time_limit($this->_time_limit);
        }

        foreach ($toStack as $to) {
            IfwPsn_Wp_Proxy_Action::doAction('ifwpsn_callback_email_loop_to', $to, $this);
            if (!IfwPsn_Wp_Proxy::mail(trim($to), $this->getSubject(), $this->getMessage(), $this->_getAdjustedHeaders(), $this->getAttachments())) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * @param bool $html
     */
    public function setHTML($html = true)
    {
        if (is_bool($html)) {
            if ($html == true) {
                IfwPsn_Wp_Proxy_Action::add('phpmailer_init', array($this, 'phpMailerEnableHtml'));
            }
            $this->_isHTML = $html;
        }
    }

    /**
     * @return bool
     */
    public function isHTML()
    {
        return $this->_isHTML === true;
    }

    /**
     * @param PHPMailer $phpmailer
     */
    public function phpMailerEnableHtml(PHPMailer $phpmailer)
    {
        if ($this->_altbody != null) {
            $phpmailer->AltBody = $this->_altbody;
        }
        $phpmailer->IsHTML(true);
    }
}
