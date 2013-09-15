<?php
/**
 * Notification manager
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @copyright   Copyright (c) 2012-2013 ifeelweb.de
 * @version     $Id$
 * @package     Psn_Notification
 */ 
class Psn_Notification_Manager
{
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var array
     */
    protected $_services = array();

    /**
     * @var string
     */
    protected $_statusBefore;

    /**
     * @var string
     */
    protected $_statusAfter;



    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public function __construct(Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
        $this->_init();
    }

    protected function _init()
    {
        Ifw_Wp_Proxy_Filter::add('transition_post_status', array($this, 'handlePostStatusTransition'), 10, 3);
        Ifw_Wp_Proxy_Filter::add('psn_service_email_body', array($this, 'filterEmailBody'), 10, 3);
        Ifw_Wp_Proxy_Filter::add('psn_service_email_subject', array($this, 'filterEmailSubject'), 10, 3);
        $this->_loadServices();
    }

    /**
     * load default services
     */
    protected function _loadServices()
    {
        $this->addService(new Psn_Notification_Service_Email());
        Ifw_Wp_Proxy::doAction('psn_after_load_services', $this);
    }

    /**
     * @param $statusAfter
     * @param $statusBefore
     * @param $post
     */
    public function handlePostStatusTransition($statusAfter, $statusBefore, $post)
    {
        $this->_statusBefore = $statusBefore;
        $this->_statusAfter = $statusAfter;

        $activeRules = Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->filter('active')->find_many();
        
        if (Psn_Model_Rule::hasMax()) {
            $activeRules = array_slice($activeRules, 0, Psn_Model_Rule::getMax());
        }

        /**
         * @var $rule Psn_Model_Rule
         */
        foreach($activeRules as $rule) {

            if ($this->_pm->hasOption('psn_ignore_status_inherit')) {
                $rule->setIgnoreInherit(true);
            }

            if ($rule->matchesPostType($post->post_type) && $rule->matchesStatus($statusBefore, $statusAfter)) {

                // rule matches
                Ifw_Wp_Proxy_Action::add('psn_notification_placeholders', array($this, 'addPlaceholders'));

                /**
                 * @var $service Psn_Notification_Service_Interface
                 */
                foreach($this->getServices() as $service) {
                    $service->execute($rule, $post);
                }
            }
        }
    }

    /**
     * @param $placeholders
     * @return array
     */
    public function addPlaceholders(array $placeholders)
    {
        return array_merge($placeholders, array(
            'post_status_before' => $this->_statusBefore,
            'post_status_after' => $this->_statusAfter,
        ));
    }

    /**
     * @param $subject
     * @return string
     */
    public function filterEmailSubject($subject)
    {
        $subject = $this->_handleSpecialChars($subject);

        return $subject;
    }

    /**
     * @param $body
     * @return string
     */
    public function filterEmailBody($body)
    {
        $body = $this->_handleSpecialChars($body);

        if (!$this->_pm->isPremium()) {
            $body .= PHP_EOL . PHP_EOL .
                sprintf(__('This email was sent by WordPress plugin "%s". Visit the plugin homepage: %s'),
                $this->_pm->getEnv()->getName(),
                $this->_pm->getEnv()->getHomepage()
                );
        }
        return $body;
    }

    /**
     * @param $string
     * @return string
     */
    protected function _handleSpecialChars($string)
    {
        return strtr($string, array(
            '&#039;' => '\'',
        ));
    }

    /**
     * @param Psn_Notification_Service_Interface $service
     */
    public function addService(Psn_Notification_Service_Interface $service)
    {
        array_push($this->_services, $service);
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return $this->_services;
    }
}
