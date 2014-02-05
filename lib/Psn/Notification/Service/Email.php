<?php
/**
 * This class handles the placeholders replacement
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) ifeelweb.de
 * @package     Psn_Notification
 */
class Psn_Notification_Service_Email implements Psn_Notification_Service_Interface
{
    /**
     * @var Psn_Model_Rule
     */
    protected $_rule;

    /**
     * @var object
     */
    protected $_post;

    /**
     * @var Ifw_Wp_Email
     */
    protected $_email;

    /**
     * @var string
     */
    protected $_body;

    /**
     * @var string
     */
    protected $_subject;

    /**
     * @var string
     */
    protected $_to;

    /**
     * @var string
     */
    protected $_cc;

    /**
     * @var string
     */
    protected $_bcc;

    /**
     * @var Psn_Notification_Placeholders
     */
    protected $_replacer;



    /**
     * @param Psn_Model_Rule $rule
     * @param $post
     */
    public function execute(Psn_Model_Rule $rule, $post)
    {
        if ((int)$rule->get('service_email') !== 1) {
            return;
        }

        $this->_rule = $rule;
        $this->_post = $post;
        $this->_replacer = new Psn_Notification_Placeholders($post);

        $this->_prepareData($rule, $post);

        if(!empty($this->_to)) {
            // send email

            // create email object
            $this->_email = new Ifw_Wp_Email();

            $this->_email->setTo($this->_to)
                ->setSubject($this->_subject)
                ->setMessage($this->_body)
            ;

            if ($this->_cc !== null) {
                $this->_email->setCc($this->_cc);
            }
            if ($this->_bcc !== null) {
                $this->_email->setBcc($this->_bcc);
            }

            Ifw_Wp_Proxy_Action::doAction('psn_before_notification_email_send', $this);

            if ($this->_email->send()) {
                // mail sent successfully
                Ifw_Wp_Proxy_Action::doAction('psn_notification_email_sent', $this);
            } else {
                // email could not be sent
                Ifw_Wp_Proxy_Action::doAction('psn_notification_email_send_error', $this);
            }

            Ifw_Wp_Proxy_Action::doAction('psn_after_notification_email_send', $this);
        }
    }

    /**
     * @param Psn_Model_Rule $rule
     * @param $post
     */
    protected function _prepareData(Psn_Model_Rule $rule, $post)
    {
        $this->_body = Ifw_Wp_Proxy_Filter::apply('psn_service_email_body', $this->_replacer->replace($rule->getNotificationBody()));
        $this->_subject = Ifw_Wp_Proxy_Filter::apply('psn_service_email_subject', $this->_replacer->replace($rule->getNotificationSubject()));

        // to
        $recipient = $rule->get('recipient');

        switch ($recipient) {
            case 'admin':
                $to = Ifw_Wp_Proxy_Blog::getAdminEmail();
                break;
            case 'author':
                $to = Ifw_Wp_Proxy_User::getEmail($post->post_author);
                break;
            default:
                $to = Ifw_Wp_Proxy_Filter::apply('psn_service_email_recipient', $rule);
                $to = $this->_replacer->replace($to);
                break;
        }
        $this->_to = $to;


        if ($rule->get('cc') != '') {
            // for supporting [blog_admin_email] / [author_email] / [current_user_email]
            $this->_cc = $this->_replacer->replace($rule->get('cc'));
        }
        if ($rule->get('bcc') != '') {
            // for supporting [blog_admin_email] / [author_email] / [current_user_email]
            $this->_bcc = $this->_replacer->replace($rule->get('bcc'));
        }
    }

    /**
     * @return \Psn_Notification_Placeholders
     */
    public function getReplacer()
    {
        return $this->_replacer;
    }

    /**
     * @param mixed $to
     */
    public function setTo($to)
    {
        $this->_to = $to;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->_to;
    }

    /**
     * @param mixed $bcc
     */
    public function setBcc($bcc)
    {
        $this->_bcc = $bcc;
    }

    /**
     * @return mixed
     */
    public function getBcc()
    {
        return $this->_bcc;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->_body = $body;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * @param mixed $cc
     */
    public function setCc($cc)
    {
        $this->_cc = $cc;
    }

    /**
     * @return mixed
     */
    public function getCc()
    {
        return $this->_cc;
    }

    /**
     * @return object
     */
    public function getPost()
    {
        return $this->_post;
    }

    /**
     * @return Psn_Model_Rule
     */
    public function getRule()
    {
        return $this->_rule;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * @return \Ifw_Wp_Email
     */
    public function getEmail()
    {
        return $this->_email;
    }

}
