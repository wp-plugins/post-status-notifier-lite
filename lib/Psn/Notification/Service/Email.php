<?php
/**
 * This class handles the placeholders replacement
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) 2012-2013 ifeelweb.de
 * @package     Psn_Notification
 */
class Psn_Notification_Service_Email implements Psn_Notification_Service_Interface
{
    public function execute(Psn_Model_Rule $rule, $post)
    {
        if ((int)$rule->get('service_email') !== 1) {
            return;
        }

        $replacer = new Psn_Notification_Placeholders($post);

        $emailBody = Ifw_Wp_Proxy_Filter::apply('psn_service_email_body', $replacer->replace($rule->getNotificationBody()));
        $emailSubject = Ifw_Wp_Proxy_Filter::apply('psn_service_email_subject', $replacer->replace($rule->getNotificationSubject()));

        switch ($rule->get('recipient')) {
            case 'admin':
                $to = Ifw_Wp_Proxy_Blog::getAdminEmail();
                break;
            case 'author':
                $to = Ifw_Wp_Proxy_User::getEmail($post->post_author);
                break;
        }

        if(!empty($to)) {
            // send email
            $email = new Ifw_Wp_Email();
            $email->setTo($to)
                ->setSubject($emailSubject)
                ->setMessage($emailBody)
            ;
            if ($rule->get('cc') != '') {
                // for supporting [blog_admin_email] / [author_email] / [current_user_email]
                $email->setCc($replacer->replace($rule->get('cc')));
            }
            if ($rule->get('bcc') != '') {
                // for supporting [blog_admin_email] / [author_email] / [current_user_email]
                $email->setBcc($replacer->replace($rule->get('bcc')));
            }

            Ifw_Wp_Proxy_Action::doAction('psn_before_notification_email_send', $email);

            if ($email->send()) {
                // mail sent successfully
                Ifw_Wp_Proxy_Action::doAction('psn_notification_email_sent', $email);
            } else {
                // email could not be sent
                Ifw_Wp_Proxy_Action::doAction('psn_notification_email_send_error', $email);
            }

            Ifw_Wp_Proxy_Action::doAction('psn_after_notification_email_send', $email);
        }

    }
}
