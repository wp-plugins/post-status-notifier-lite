<?php
/**
 * This class handles the email sending process
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) ifeelweb.de
 * @package     Psn_Notification
 */
require_once dirname(__FILE__) . '/Interface.php';

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
     * @var IfwPsn_Wp_Email
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
     * @var array
     */
    protected $_to = array();

    /**
     * @var array
     */
    protected $_cc = array();

    /**
     * @var array
     */
    protected $_bcc = array();

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
            $this->_email = new IfwPsn_Wp_Email();

            $this->_email->setTo($this->getFormattedEmails($this->_to))
                ->setSubject($this->_subject)
                ->setMessage($this->_body)
            ;

            if ($this->hasCc()) {
                $this->_email->setCc($this->getFormattedEmails($this->_cc));
            }
            if ($this->hasBcc()) {
                $this->_email->setBcc($this->getFormattedEmails($this->_bcc));
            }

            IfwPsn_Wp_Proxy_Action::doAction('psn_before_notification_email_send', $this);
            
            if ($this->_email->send()) {
                // mail sent successfully
                IfwPsn_Wp_Proxy_Action::doAction('psn_notification_email_sent', $this);
            } else {
                // email could not be sent
                IfwPsn_Wp_Proxy_Action::doAction('psn_notification_email_send_error', $this);
            }

            IfwPsn_Wp_Proxy_Action::doAction('psn_after_notification_email_send', $this);
        }
    }

    /**
     * @param Psn_Model_Rule $rule
     * @param $post
     */
    protected function _prepareData(Psn_Model_Rule $rule, $post)
    {
        $this->_body = IfwPsn_Wp_Proxy_Filter::apply('psn_service_email_body', $this->_replacer->replace($rule->getNotificationBody()));
        $this->_subject = IfwPsn_Wp_Proxy_Filter::apply('psn_service_email_subject', $this->_replacer->replace($rule->getNotificationSubject()));

        // recipient handling (To, Cc, Bcc)
        $recipientSelections = array(
            array(
                'name' => 'recipient_selection',
                'modelGetter' => 'getRecipient',
                'serviceAdder' => 'addTo',
                'custom_field_name' => 'to'
            ),
            array(
                'name' => 'cc_selection',
                'modelGetter' => 'getCcSelect',
                'serviceAdder' => 'addCc',
                'custom_field_name' => 'cc'
            ),
            array(
                'name' => 'bcc_selection',
                'modelGetter' => 'getBccSelect',
                'serviceAdder' => 'addBcc',
                'custom_field_name' => 'bcc'
            ),
        );

        foreach ($recipientSelections as $recSel) {

            $recipient = $rule->$recSel['modelGetter']();

            if (in_array('admin', $recipient)) {
                $this->$recSel['serviceAdder'](IfwPsn_Wp_Proxy_Blog::getAdminEmail());
            }
            if (in_array('author', $recipient)) {
                $this->$recSel['serviceAdder'](IfwPsn_Wp_Proxy_User::getEmail($post->post_author));
            }

            // handle dynamic recipients managed by modules
            IfwPsn_Wp_Proxy_Action::doAction('psn_service_email_'. $recSel['name'], $this);

            // check for custom recipient
            $custom_recipient = $rule->get($recSel['custom_field_name']);
            if (!empty($custom_recipient)) {
                $this->$recSel['serviceAdder']($this->_replacer->replace($custom_recipient));
            }
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
        if (is_array($to)) {
            $this->_to = $to;
        }
    }

    /**
     * @param string $to
     */
    public function addTo($to)
    {
        array_push($this->_to, $to);
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->_to;
    }

    /**
     * @param mixed $cc
     */
    public function setCc($cc)
    {
        if (is_array($cc)) {
            $this->_cc = $cc;
        }
    }

    /**
     * @param string $cc
     */
    public function addCc($cc)
    {
        array_push($this->_cc, $cc);
    }

    /**
     * @return mixed
     */
    public function getCc()
    {
        return $this->_cc;
    }

    /**
     * @return bool
     */
    public function hasCc()
    {
        return count($this->_cc) > 0;
    }

    /**
     * @param mixed $bcc
     */
    public function setBcc($bcc)
    {
        if (is_array($bcc)) {
            $this->_bcc = $bcc;
        }
    }

    /**
     * @param string $bcc
     */
    public function addBcc($bcc)
    {
        array_push($this->_bcc, $bcc);
    }

    /**
     * @return mixed
     */
    public function getBcc()
    {
        return $this->_bcc;
    }

    /**
     * @return bool
     */
    public function hasBcc()
    {
        return count($this->_bcc) > 0;
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
     * @return \IfwPsn_Wp_Email
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * @param array $emails
     * @return string
     */
    public function getFormattedEmails(array $emails)
    {
        return implode(',' , $emails);
    }
}
