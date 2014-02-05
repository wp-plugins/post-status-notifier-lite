<?php
/**
 *
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) ifeelweb.de
 * @package     Psn_Admin
 */
class Psn_Admin_Form_NotificationRule extends Ifw_Zend_Form
{
    /**
     * @var array
     */
    protected $_fieldDecorators;

    /**
     * @var bool
     */
    protected $_hideNonPublicPostTypes = false;



    /**
     * @param null $options
     */
    public function __construct($options = null)
    {
        if (isset($options['hide_nonpublic_posttypes']) && $options['hide_nonpublic_posttypes'] === true) {
            $this->setHideNonPublicPostTypes(true);
            unset($options['hide_nonpublic_posttypes']);
        }
        parent::__construct($options);
    }

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

        $this->_fieldDecorators = array(
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
            'decorators'     => $this->getFieldDecorators(),
            'order'          => 10
        ));


        $postTypeOptions = array();
        if ($this->isHideNonPublicPostTypes()) {
            $postTypeOptions['public'] = true;
        }

        $postType = $this->createElement('select', 'posttype');
        $postTypeOptions = array_merge(array('all' => __('all types', 'psn')), Ifw_Wp_Proxy_Post::getAllTypesWithLabels($postTypeOptions));
        $postType
            ->setLabel(__('Post type', 'psn'))
            ->setDecorators($this->getFieldDecorators())
            ->setFilters(array('StringTrim', 'StripTags'))
            ->addMultiOptions($postTypeOptions)
            ->setOrder(20);
        $this->addElement($postType);

        $statusValues = array_merge(
            array('anything' => __('anything', 'psn'), 'new' => __('New', 'ifw'), 'not_published' => __('Not published', 'psn')),
            Ifw_Wp_Proxy_Post::getAllStatusesWithLabels(array('show_domain' => true))
        );

        $statusBefore = $this->createElement('select', 'status_before');
        $statusBefore
            ->setLabel(__('Status before', 'psn'))
            ->setDecorators($this->getFieldDecorators())
            ->setFilters(array('StringTrim', 'StripTags'))
            ->addMultiOptions($statusValues)
            ->setOrder(30);
        $this->addElement($statusBefore);

        $statusAfter = $this->createElement('select', 'status_after');
        $statusAfter
            ->setLabel(__('Status after', 'psn'))
            ->setDecorators($this->getFieldDecorators())
            ->setFilters(array('StringTrim', 'StripTags'))
            //->setValidators(array(new Psn_Admin_Form_Validate_StatusTransition()))
            ->addMultiOptions($statusValues)
            ->setOrder(40);
        $this->addElement($statusAfter);

        $this->addElement('text', 'notification_subject', array(
            'label'          => __('Subject', 'psn'),
            'description'    => __('Open the help menu in the upper right corner to see a list of all supported placeholders.', 'psn'),
            'required'       => true,
            'filters'        => array('StringTrim', 'StripTags'),
            'maxlength'      => 200,
            'decorators'     => $this->getFieldDecorators(),
            'order'          => 50
        ));

        $this->addElement('textarea', 'notification_body', array(
            'label'          => __('Text', 'psn'),
            'description'    => __('Open the help menu in the upper right corner to see a list of all supported placeholders.', 'psn'),
            'required'       => true,
            'filters'        => array('StringTrim', 'HtmlEntities'),
            'cols'           => 80,
            'rows'           => 10,
            'decorators'     => $this->getFieldDecorators(),
            'order'          => 60
        ));

        $recipient = $this->createElement('select', 'recipient');
        $recipient
            ->setLabel(__('Recipient', 'psn'))
            ->setDecorators($this->getFieldDecorators())
            ->setFilters(array('StringTrim', 'StripTags'))
            ->addMultiOptions(Ifw_Wp_Proxy_Filter::apply('psn_rule_form_recipients_options', array(
                'admin'  => __('Blog admin', 'psn'),
                'author' => __('Post author', 'psn'),
            )))
            ->setOrder(70);
        $this->addElement($recipient);

        $this->addElement('textarea', 'cc', array(
            'label'          => __('Cc', 'psn'),
            'description'    => Ifw_Wp_Proxy_Filter::apply('psn_rule_form_description_cc',
                __('Add additional recipient emails. Comma separated. Supports placeholders [author_email], [blog_admin_email] and [current_user_email].', 'psn')),
            'filters'        => array('StringTrim', 'HtmlEntities',
                new Psn_Admin_Form_Filter_Cc(Ifw_Wp_Plugin_Manager::getInstance('Psn')->isPremium())),
            'cols'           => 80,
            'rows'           => 1,
            'decorators'     => $this->getFieldDecorators(),
            'order'          => 80
        ));

        $this->addElement('textarea', 'bcc', array(
            'label'          => __('Bcc', 'psn'),
            'description'    => Ifw_Wp_Proxy_Filter::apply('psn_rule_form_description_bcc',
                __('Add bcc recipient emails. Comma separated. Supports placeholders [author_email], [blog_admin_email] and [current_user_email].', 'psn')),
            'filters'        => array('StringTrim', 'HtmlEntities',
                new Psn_Admin_Form_Filter_Bcc(Ifw_Wp_Plugin_Manager::getInstance('Psn')->isPremium())),
            'cols'           => 80,
            'rows'           => 1,
            'decorators'     => $this->getFieldDecorators(),
            'order'          => 90
        ));

        $active = $this->createElement('checkbox', 'active');
        $active->setLabel(__('Active', 'psn'))
            ->setDecorators($this->getFieldDecorators())
            ->setDescription(__('Only active rules take affect on post transition changes', 'psn'))
            ->setChecked(true)
            ->setCheckedValue(1)
            ->setOrder(100)
            ;
        $this->addElement($active);

        $email = $this->createElement('checkbox', 'service_email');
        $email->setLabel(__('Email', 'psn'))
            ->setDecorators($this->getFieldDecorators())
            ->setDescription(__('When the rule matches, an email will be send to the recipient with subject and text', 'psn'))
            ->setChecked(true)
            ->setCheckedValue(1)
            ->setOrder(110)
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
            ),
            'order' => 120
        ));

    }

    /**
     * @return array
     */
    public function getFieldDecorators()
    {
        return $this->_fieldDecorators;
    }

    /**
     * @param boolean $hideNonPublicPostTypes
     */
    public function setHideNonPublicPostTypes($hideNonPublicPostTypes)
    {
        if (is_bool($hideNonPublicPostTypes)) {
            $this->_hideNonPublicPostTypes = $hideNonPublicPostTypes;
        }
    }

    /**
     * @return boolean
     */
    public function isHideNonPublicPostTypes()
    {
        return $this->_hideNonPublicPostTypes === true;
    }


}
