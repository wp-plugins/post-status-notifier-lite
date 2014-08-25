<?php
/**
 * Notification manager
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @copyright   Copyright (c) ifeelweb.de
 * @version     $Id$
 * @package     Psn_Notification
 */ 
class Psn_Notification_Manager
{
    /**
     *
     */
    const POST_CUSTOM_FIELD_KEY_BLOCK_NOTIFICATIONS = 'psn-block-notifications';

    /**
     * @var IfwPsn_Wp_Plugin_Manager
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
     * @var array
     */
    protected $_replacerInstances = array();



    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     */
    public function __construct(IfwPsn_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
        $this->_init();
    }

    protected function _init()
    {
        IfwPsn_Wp_Proxy_Filter::add('transition_post_status', array($this, 'handlePostStatusTransition'), 10, 3);
        // custom trigger. can be used to manually trigger the notification services on a post.
        IfwPsn_Wp_Proxy_Filter::add('psn_trigger_notification_manager', array($this, 'handlePostStatusTransition'), 10, 3);

        IfwPsn_Wp_Proxy_Filter::add('psn_service_email_body', array($this, 'filterEmailBody'), 10, 3);
        IfwPsn_Wp_Proxy_Filter::add('psn_service_email_subject', array($this, 'filterEmailSubject'), 10, 3);

        // add replacer filters
        IfwPsn_Wp_Proxy_Filter::add('psn_notification_placeholders', array($this, 'addPlaceholders'));
        IfwPsn_Wp_Proxy_Filter::add('psn_notification_placeholders', array($this, 'filterPlaceholders'));
        IfwPsn_Wp_Proxy_Filter::add('psn_notification_dynamic_placeholders', array($this, 'filterPlaceholders'));

        $this->_loadServices();
    }

    /**
     * load default services
     */
    protected function _loadServices()
    {
        require_once $this->_pm->getPathinfo()->getRootLib() . 'Psn/Notification/Service/Email.php';

        $this->addService(new Psn_Notification_Service_Email());
        IfwPsn_Wp_Proxy_Action::doAction('psn_after_load_services', $this);
    }

    /**
     * @param $statusAfter
     * @param $statusBefore
     * @param $post
     */
    public function handlePostStatusTransition($statusAfter, $statusBefore, $post)
    {
        if ($this->isBlockNotifications($post->ID)) {
            // skip if option to block notifications is set
            return;
        }

        $this->_pm->getErrorHandler()->enableErrorReporting();

        $this->_statusBefore = $statusBefore;
        $this->_statusAfter = $statusAfter;

        // get all active rules
        $activeRules = IfwPsn_Wp_ORM_Model::factory('Psn_Model_Rule')->filter('active')->find_many();
        
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

            if ($rule->matches($post, $statusBefore, $statusAfter)) {

                // rule matches

                // set the replacer
                $rule->setReplacer($this->getReplacer($post));

                /**
                 * Execute all registered notification services
                 *
                 * @var $service Psn_Notification_Service_Interface
                 */
                foreach($this->getServices() as $service) {
                    $service->execute($rule, $post);
                }
            }
        }

        $this->_pm->getErrorHandler()->disableErrorReporting();
    }

    /**
     * @param $post
     * @return Psn_Notification_Placeholders
     */
    public function getReplacer($post)
    {
        if (!isset($this->_replacerInstances[$post->ID])) {
            $this->_replacerInstances[$post->ID] = new Psn_Notification_Placeholders($post);
        }

        return $this->_replacerInstances[$post->ID];
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
     * @param $placeholders
     * @return array
     */
    public function filterPlaceholders(array $placeholders)
    {
        $filters = $this->_pm->getBootstrap()->getOptionsManager()->getOption('placeholders_filters');

        if (!empty($filters)) {

            $counter = 0;
            foreach (preg_split("/((\r?\n)|(\r\n?))/", $filters) as $filter) {
                if (!$this->_pm->isPremium() && $counter >= 1) {
                    break;
                }

                preg_match_all('/\[([A-Za-z0-9_-]+?)\]/', $filter, $match);

                if (isset($match[0][0]) && isset($match[1][0])) {
                    $placeholder_tag = $match[0][0];
                    $placeholder_name = $match[1][0];

                    if (isset($placeholders[$placeholder_name])) {
                        $filter_string = str_replace($placeholder_tag, '"'. $placeholders[$placeholder_name] . '"', $filter);
                        if (!empty($filter_string)) {
                            if ($filter_string[0] != '{') {
                                $filter_string = '{{ '. $filter_string . ' }}';
                            }

                            $placeholders[$placeholder_name] = IfwPsn_Wp_Tpl::renderString($filter_string);
                        }
                    }
                }
                $counter++;
            }

        }
        
        return $placeholders;
    }

    /**
     * @param $subject
     * @param Psn_Notification_Service_Email $email
     * @return string
     */
    public function filterEmailSubject($subject, Psn_Notification_Service_Email $email)
    {
        $subject = $this->_handleSpecialChars($subject);

        return $subject;
    }

    /**
     * @param $body
     * @param Psn_Notification_Service_Email $email
     * @return string
     */
    public function filterEmailBody($body, Psn_Notification_Service_Email $email)
    {
        $body = $this->_handleSpecialChars($body);

        if (!$this->_pm->isPremium()) {
            // please respect my work and buy the premium version if you want this plugin to stay alive!
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

    /**
     * @param null $postId
     * @return bool
     */
    public function isBlockNotifications($postId = null)
    {
        if ($postId == null) {
            global $post;
            $postId = $post->ID;
        } else {
            $postId = intval($postId);
        }

        if (!empty($postId)) {
            $result = get_post_meta($postId, self::POST_CUSTOM_FIELD_KEY_BLOCK_NOTIFICATIONS, true);
            return $result === '1';
        }

        return false;
    }
}
