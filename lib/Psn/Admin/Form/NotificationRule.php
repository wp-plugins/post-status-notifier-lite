<?php
/**
 *
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) 2012-2013 ifeelweb.de
 * @package     Psn_Admin
 */
class Psn_Admin_Form_NotificationRule extends Ifw_Zend_Form
{
    /**
     * @return void
     */
    public function init()
    {
        $this->setMethod('post')->setName('psn_form_rule')->setAttrib('accept-charset', 'utf-8');

        $this->setAttrib('class', 'ifw-wp-zend-form-ul');

        $this->setDecorators(array(
            'FormElements',
            'Form'
        ));

        $fieldDecorators = array(
            new Ifw_Zend_Form_Decorator_SimpleInput(),
            array('HtmlTag', array('tag' => 'li')),
            'Errors',
            'Description'
        );

        $this->addElement('text', 'name', array(
            'label'          => __('Rule name', 'psn'),
            'description'    => __('Name of the rule', 'psn'),
            'required'       => true,
            'filters'        => array('StringTrim', 'StripTags'),
            'maxlength'      => 80,
            'validators'     => $_GET['appaction'] == 'create' ? array(new Psn_Admin_Form_Validate_Max()) : array(),
            'decorators'     => $fieldDecorators
        ));

        $postType = $this->createElement('select', 'posttype');
        $postTypeOptions = array_merge(array('all' => __('all types', 'psn')), Ifw_Wp_Proxy_Post::getAllTypesWithLabels());
        $postType
            ->setLabel(__('Post type', 'psn'))
            ->setDecorators($fieldDecorators)
            ->setFilters(array('StringTrim', 'StripTags'))
            ->addMultiOptions($postTypeOptions);
        $this->addElement($postType);


        $statusValues = array_merge(
            array('anything' => __('anything', 'psn'), 'new' => __('New', 'ifw')),
            Ifw_Wp_Proxy_Post::getAllStatusesWithLabels()
        );

        $statusBefore = $this->createElement('select', 'status_before');
        $statusBefore
            ->setLabel(__('Status before', 'psn'))
            ->setDecorators($fieldDecorators)
            ->setFilters(array('StringTrim', 'StripTags'))
            ->addMultiOptions($statusValues);
        $this->addElement($statusBefore);

        $statusAfter = $this->createElement('select', 'status_after');
        $statusAfter
            ->setLabel(__('Status after', 'psn'))
            ->setDecorators($fieldDecorators)
            ->setFilters(array('StringTrim', 'StripTags'))
            //->setValidators(array(new Psn_Admin_Form_Validate_StatusTransition()))
            ->addMultiOptions($statusValues);
        $this->addElement($statusAfter);

        $this->addElement('text', 'notification_subject', array(
            'label'          => __('Subject', 'psn'),
            'description'    => __('Open the help menu in the upper right corner to see a list of all supported placeholders.', 'psn'),
            'required'       => true,
            'filters'        => array('StringTrim', 'StripTags'),
            'maxlength'      => 200,
            'decorators'     => $fieldDecorators
        ));

        $this->addElement('textarea', 'notification_body', array(
            'label'          => __('Text', 'psn'),
            'description'    => __('Open the help menu in the upper right corner to see a list of all supported placeholders.', 'psn'),
            'required'       => true,
            'filters'        => array('StringTrim', 'HtmlEntities'),
            'cols'           => 80,
            'rows'           => 10,
            'decorators'     => $fieldDecorators
        ));

        $recipient = $this->createElement('select', 'recipient');
        $recipient
            ->setLabel(__('Recipient', 'psn'))
            ->setDecorators($fieldDecorators)
            ->setFilters(array('StringTrim', 'StripTags'))
            ->addMultiOptions(Ifw_Wp_Proxy_Filter::apply('psn_rule_form_recipients_options', array(
                'admin'  => __('Blog admin', 'psn'),
                'author' => __('Post author', 'psn'),
            )));
        $this->addElement($recipient);

        $this->addElement('textarea', 'cc', array(
            'label'          => __('Cc', 'psn'),
            'description'    => Ifw_Wp_Proxy_Filter::apply('psn_rule_form_description_cc',
                __('Add additional recipient emails. Comma separated. Supports placeholders [author_email], [blog_admin_email] and [current_user_email].', 'psn')),
            'filters'        => array('StringTrim', 'HtmlEntities',
                new Psn_Admin_Form_Filter_Cc(Ifw_Wp_Plugin_Manager::getInstance('Psn')->isPremium())),
            'cols'           => 80,
            'rows'           => 1,
            'decorators'     => $fieldDecorators
        ));

        $this->addElement('textarea', 'bcc', array(
            'label'          => __('Bcc', 'psn'),
            'description'    => Ifw_Wp_Proxy_Filter::apply('psn_rule_form_description_bcc',
                __('Add bcc recipient emails. Comma separated. Supports placeholders [author_email], [blog_admin_email] and [current_user_email].', 'psn')),
            'filters'        => array('StringTrim', 'HtmlEntities',
                new Psn_Admin_Form_Filter_Bcc(Ifw_Wp_Plugin_Manager::getInstance('Psn')->isPremium())),
            'cols'           => 80,
            'rows'           => 1,
            'decorators'     => $fieldDecorators
        ));

        $active = $this->createElement('checkbox', 'active');
        $active->setLabel(__('Active', 'psn'))
            ->setDecorators($fieldDecorators)
            ->setDescription(__('Only active rules take affect on post transition changes', 'psn'))
            ->setChecked(true)
            ->setCheckedValue(1)
            ;
        $this->addElement($active);

        $email = $this->createElement('checkbox', 'service_email');
        $email->setLabel(__('Email', 'psn'))
            ->setDecorators($fieldDecorators)
            ->setDescription(__('When the rule matches, an email will be send to the recipient with subject and text', 'psn'))
            ->setChecked(true)
            ->setCheckedValue(1)
        ;
        $this->addElement($email);


        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => __('Add rule', 'psn'),
            'class'    => 'button-primary',
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'li')),
            )
        ));

    }
}
