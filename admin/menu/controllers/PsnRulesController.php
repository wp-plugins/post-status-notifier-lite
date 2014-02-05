<?php
/**
 * Rules controller
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $$Id$$
 * @package  Ifw_Wp
 */
class PsnRulesController extends PsnApplicationController
{
    /**
     * @var IfwZend_Form
     */
    protected $_form;

    /**
     * @var Ifw_Wp_Plugin_Screen_Option_PerPage
     */
    protected $_perPage;



    /**
     * (non-PHPdoc)
     * @see IfwZend_Controller_Action::preDispatch()
     */
    public function preDispatch()
    {
        if ($this->_request->getActionName() == 'index') {

            if (isset($_POST['action']) && $_POST['action'] != '-1') {
                $action = $this->_request->getPost('action');
            } elseif (isset($_POST['action2']) && $_POST['action2'] != '-1') {
                $action = $this->_request->getPost('action2');
            } else {
                $action = false;
            }

            if ( $action == 'delete' && is_array($this->_request->getPost('rule')) ) {
                // bulk action delete
                $this->_bulkDelete($this->_request->getPost('rule'));
            } else if ( $action == 'deactivate' && is_array($this->_request->getPost('rule')) ) {
                // bulk action deactivate
                $this->_bulkDeactivate($this->_request->getPost('rule'));
            } else if ( $action == 'activate' && is_array($this->_request->getPost('rule')) ) {
                // bulk action activate
                $this->_bulkActivate($this->_request->getPost('rule'));
            } else if ( $action == 'export' && is_array($this->_request->getPost('rule')) ) {
                // bulk action activate
                $this->_bulkExport($this->_request->getPost('rule'));
            }
        }
    }

    public function onBootstrap()
    {
        if ($this->_request->getActionName() == 'index') {
            $this->_perPage = new Ifw_Wp_Plugin_Screen_Option_PerPage($this->_pm, __('Items per page', 'ifw'), 'psn_rules_per_page');
        }
    }

    public function onAdminInit()
    {
    }

    public function onCurrentScreen()
    {
    }

    public function onLoad()
    {
    }

    /**
     *
     */
    public function indexAction()
    {
        Ifw_Wp_Proxy_Script::loadAdmin('psn_rules', $this->_pm->getEnv()->getUrlAdminJs() . 'rules.js', array(), $this->_pm->getEnv()->getVersion());

        // set up contextual help
        $help = new Ifw_Wp_Plugin_Menu_Help($this->_pm);
        $help->setTitle(__('Rules', 'psn'))
            ->setHelp($this->_getDefaultHelpText())
            ->setSidebar($this->_getHelpSidebar())
            ->load();

        $listTable = new Psn_Admin_ListTable_Rules($this->_pm);
        $listTable->setItemsPerPage($this->_perPage->getOption());

        $this->view->listTable = $listTable;
        $this->view->langCreateNewRule = __('Create new rule', 'psn');
        $this->view->isPremium = $this->_pm->isPremium();

        $dbPatcher = new Psn_Patch_Database();
        $this->view->dbPatcher = $dbPatcher;
    }

    /**
     * Create new rule
     */
    public function createAction()
    {
        $this->_initFormView();

        if ($this->_request->isPost()) {
            if ($this->_form->isValid($this->_request->getPost())) {

                // request is valid, save the rule
                $rule = Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->create($this->_getFormValues());
                $rule->save();

                $this->getMessenger()->addMessage(
                    sprintf(__('Rule <b>%s</b> has been saved successfully.', 'psn'), $rule->get('name')));

                $this->_gotoRoute('rules');
            }
        }

        $this->view->form = $this->_form;
    }

    /**
     * Edit rules
     */
    public function editAction()
    {
        $this->_initFormView();

        $id = (int)$this->_request->get('id');

        $rule = Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->find_one($id);
        $ruleNameBefore = $rule->get('name');

        $categories = $rule->getCategories();
        if ($categories === null) {
            $categories = array();
        }

        Ifw_Wp_Proxy_Script::localize('psn_rule_form', 'psn_taxonomies_selected', $categories);


        $this->_form->setDefaults($rule->as_array());

        if ($this->_request->isPost()) {
            if ($this->_form->isValid($this->_request->getPost())) {

                // request is valid, save the changes
                $rule->hydrate($this->_getFormValues());
                $rule->id = $id;
                $rule->save();

                $this->getMessenger()->addMessage(
                    sprintf(__('Rule <b>%s</b> has been updated successfully.', 'psn'), $ruleNameBefore));

                $this->_gotoRoute('rules');
            }
        }

        $this->view->form = $this->_form;
    }

    /**
     * Prepares the submitted values for saving
     * @return array
     */
    protected function _getFormValues()
    {
        $values = $this->_form->getValues();
        $posttype = $values['posttype'];

        $categories = array();

        if ($this->_request->has('category_include_' . $posttype) && $this->_pm->isPremium()) {
            $categoriesInclude = $this->_request->get('category_include_' . $posttype);
            $categoriesInclude = array_map('intval', $categoriesInclude);
            if (count($categoriesInclude) > 0) {
                sort($categoriesInclude);
                $categories['include'] = $categoriesInclude;
            }
        }
        if ($this->_request->has('category_exclude_' . $posttype) && $this->_pm->isPremium()) {
            $categoriesExclude = $this->_request->get('category_exclude_' . $posttype);
            $categoriesExclude = array_map('intval', $categoriesExclude);
            if (count($categoriesExclude) > 0) {
                sort($categoriesExclude);
                $categories['exclude'] = $categoriesExclude;
            }
        }

        if (empty($categories)) {
            $values['categories'] = null;
        } else {
            $values['categories'] = serialize($categories);
        }

        if (isset($values['to']) && $values['recipient'] != 'individual_email') {
            $values['to'] = null;
        }

        return $values;
    }

    /**
     * Initializes commonly used properties
     */
    protected function _initFormView()
    {
        $dbPatcher = new Psn_Patch_Database();
        $this->view->dbPatcher = $dbPatcher;

        if (!$this->_pm->isPremium()) {
            Ifw_Wp_Proxy_Filter::add('psn_rule_form_description_cc', create_function('$var','return $var . " " .
                __("Limited to 1. Get the Premium version for unlimited Cc emails.", "psn");'));
            Ifw_Wp_Proxy_Filter::add('psn_rule_form_description_bcc', create_function('$var','return $var . " " .
                __("(Premium feature)", "psn");'));
        }

        $formOptions = array();
        if ($this->_pm->getOptionsManager()->getOption('psn_hide_nonpublic_posttypes') != null) {
            $formOptions['hide_nonpublic_posttypes'] = true;
        }

        $this->_form = new Psn_Admin_Form_NotificationRule($formOptions);

        if (!$this->_pm->isPremium()) {
            $this->_form->getElement('recipient')->setDescription(__('Get additional recipients like user roles (including custom roles) or all users with the Premium version.', 'psn'));
        }

        $this->_helper->viewRenderer('form');
        $help = new Ifw_Wp_Plugin_Menu_Help($this->_pm);
        $help->setTitle(__('Placeholders', 'psn'))
            ->setHelp($this->_getHelpTextPlaceholders())
            ->setSidebar($this->_getHelpSidebar())
            ->load();

        $this->view->langListOfPlaceholdersLabel = __('Show list of placeholders available for subject and text', 'psn');
        $this->view->langListOfPlaceholdersLink = __('List of placeholders', 'psn');

        $this->view->langHelp = __('Help', 'ifw');
        if (Psn_Model_Rule::hasMax() && Psn_Model_Rule::reachedMax() && $this->getRequest()->getActionName() == 'create') {
            $this->view->maxReached = __(sprintf('You reached the maximum number of rules (%s) for the free version. Get the <a href="%s" target="_blank">Premium Version</a> for unlimmited rules and more features.', Psn_Model_Rule::getMax(), $this->_pm->getConfig()->plugin->premiumUrl), 'psn');
        }

        if ($this->_request->getActionName() == 'create') {
            $this->view->langHeadline = __('Create new rule', 'psn');

            Ifw_Wp_Proxy_Script::loadAdmin('psn_rule_examples', $this->_pm->getEnv()->getUrlAdminJs() . 'rule_examples.js', array(), $this->_pm->getEnv()->getVersion());
            Ifw_Wp_Proxy_Script::localize('psn_rule_examples', 'PsnExampleRule', array(
                'ThePendingPost' => __('The pending post', 'psn'),
                'ThePendingPostSubject' => __('[blog_name]: New post is waiting for review', 'psn'),
                'ThePendingPostBody' => str_replace('<br>', "\n", __('Howdy admin,<br>there is a new post by [author_display_name] waiting for review:<br>"[post_title]".<br><br>Here is the permalink: [post_permalink]<br><br>The author\'s email address is [author_email]<br><br>[blog_wpurl]', 'psn')),
                'TheHappyAuthor' => __('The happy author', 'psn'),
                'TheHappyAuthorSubject' => __('Your post on [blog_name] got published!', 'psn'),
                'TheHappyAuthorBody' => str_replace('<br>', "\n", __('Howdy [author_display_name],<br>we are happy to tell you that your post "[post_title]" got published.<br><br>Here is the permalink: [post_permalink]<br><br>Thanks for your good work,<br>your [blog_name]-Team<br><br>[blog_wpurl]', 'psn')),
                'ThePedanticAdmin' => __('The pedantic admin', 'psn'),
                'ThePedanticAdminSubject' => __('[blog_name]: Post status transition from [post_status_before] to [post_status_after]', 'psn'),
                'ThePedanticAdminBody' => str_replace('<br>', "\n", __('Howdy admin,<br>a post status transition was a detected on "[post_title]".<br><br>Status before: [post_status_before]<br>Status after: [post_status_after]<br><br>Post permalink: [post_permalink]', 'psn')),
            ));

            Ifw_Wp_Proxy_Style::loadAdmin('psn_rule_examples', $this->_pm->getEnv()->getUrlAdminCss() . 'rule_examples.css');

            $this->view->langExamples = __('Examples', 'psn');
            $this->view->langExamplesDesc = __('Click the buttons below to get an idea of how you can set up notification rules.', 'psn');
            $this->view->langExamplesRuleThePendingPost = __('The pending post', 'psn');
            $this->view->langExamplesRuleThePendingPostDesc = __('This rule sends a notification when a new post got submitted for review.', 'psn');
            $this->view->langExamplesRuleTheHappyAuthor = __('The happy author', 'psn');
            $this->view->langExamplesRuleTheHappyAuthorDesc = __('This rule sends an email to the author of a post when it got published.', 'psn');
            $this->view->langExamplesRuleThePedanticAdmin = __('The pedantic admin', 'psn');
            $this->view->langExamplesRuleThePedanticAdminDesc = __('This rule is for blog admins who want to be informed about every single post status change.', 'psn');
        } else {
            $this->view->langHeadline = __('Edit notification rule', 'psn');
            $this->_form->getElement('submit')->setLabel(__('Update', 'psn'));
        }

        Ifw_Wp_Proxy_Script::loadAdmin('psn_rule_form', $this->_pm->getEnv()->getUrlAdminJs() . 'rule_form.js', array(), $this->_pm->getEnv()->getVersion());
        Ifw_Wp_Proxy_Script::localize('psn_rule_form', 'psn', array('is_premium' => $this->_pm->isPremium()));
        Ifw_Wp_Proxy_Script::localize('psn_rule_form', 'psn_taxonomies', array_merge(
            Ifw_Wp_Proxy_Post::getAllTypesCategories(),
            array(
                'lang_Categories' => __('Categories', 'psn'),
                'lang_categories_help' => sprintf(__('To select multiple categories hold down the control button (ctrl) on Windows or command button (cmd) on Mac.<br>If nothing is selected, all categories get included.<br>Exclude is dominant. See the <a href="%s" target="_blank">docs</a> for more details.', 'psn'),
                    'http://docs.ifeelweb.de/post-status-notifier/rules.html#category-filter'),
                'lang_include_categories' => __('Include categories', 'psn'),
                'lang_exclude_categories' => __('Exclude categories', 'psn'),
                'lang_select_all' => __('select all', 'psn'),
                'lang_remove_all' => __('remove all', 'psn'),
                'lang_no_categories' => __('Post type "%s" has no categories.', 'psn'),
                'lang_premium_feature' => sprintf(__('This is a <a href="%s" target="_blank">Premium</a> feature.', 'psn'), $this->_pm->getConfig()->plugin->premiumUrl),
            ))
        );

        Ifw_Wp_Proxy_Action::doPlugin($this->_pm, 'rule_form', $this->_form);
    }

    /**
     * Deletes a rule
     */
    public function deleteAction()
    {
        $rule = Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->find_one((int)$this->_request->get('id'));
        $rule->delete();

        $this->_gotoRoute('rules');
    }

    /**
     * Copies a rule
     */
    public function copyAction()
    {
        $rule = Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->find_one((int)$this->_request->get('id'));
        $values = $rule->as_array();

        unset($values['id']);

        $newNameFormat = '%s [%s%s]';

        $count = Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->where_like('name', sprintf($newNameFormat, $values['name'], __('Dupliacte', 'psn'), '%') . '%')->count();

        $copyCount = '';
        if ($count > 0) {
            $copyCount = $count + 1;
        }
        $values['name'] = sprintf($newNameFormat, $values['name'], __('Dupliacte', 'psn'), $copyCount);

        if ($this->_pm->getOptionsManager()->getOption('psn_deactivate_copied_rules') !== null) {
            $values['active'] = 0;
        }

        $copy = Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->create($values);
        $copy->save();

        $this->_gotoRoute('rules');
    }

    /**
     * Imports rules
     */
    public function importAction()
    {
        $tmpFilename = $_FILES['importfile']['tmp_name'];

        // check if file was submitted
        if (empty($tmpFilename)) {
            $this->_addErrorMessage(__('Please select a valid import file.', 'psn'));
            $this->_gotoRoute('rules');
        }

        $xml = simplexml_load_file($tmpFilename);

        // check for valid xml
        if (!$xml) {
            $this->_addErrorMessage(__('Please select a valid import file.', 'psn'));
            $this->_gotoRoute('rules');
        }

        // check if xml contains rules
        if (count($xml->{'rule'}) == 0) {
            // no rules found
            $this->_addErrorMessage(__('No rules found in import file.', 'psn'));
            $this->_gotoRoute('rules');
        }

        // get the rules
        $rules = array();

        foreach($xml->{'rule'} as $rule) {
            $tmpRule = array();
            foreach($rule as $col) {
                $tmpRule[(string)$col['name']] = (string)$col;
            }
            array_push($rules, $tmpRule);
        }

        // fetch options
        $prefix = esc_attr($this->_request->get('import_prefix'));
        $deactivate = $this->_request->get('import_deactivate');

        // create imported rules
        foreach ($rules as $rule) {
            unset($rule['id']);
            if (!empty($prefix)) {
                $rule['name'] = $prefix . $rule['name'];
            }
            if ($deactivate != null) {
                $rule['active'] = 0;
            }
            $importRule = Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->create($rule);
            $importRule->save();
        }

        @unlink($tmpFilename);

        // load rules admin
        $this->_gotoRoute('rules');
    }

    /**
     *
     */
    public function exportAction()
    {
        $id = (int)$this->_request->get('id');

        $rule = Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->find_one($id);
        $values = $rule->as_array();

        $this->_export(array($values));
    }

    /**
     * @param $rules
     * @param null $filename
     */
    protected function _export($rules, $filename = null)
    {
        $result = "<rules>\n";

        foreach ( $rules as $rule ) {
            $result .= "<rule>\n";
            foreach ($rule as $field => $value) {

                if (in_array($field, array('name', 'notification_subject', 'notification_body', 'recipient', 'to', 'cc', 'bcc', 'from', 'categories'))) {
                    $value = '<![CDATA['. $value . ']]>';
                }
                $result .= "\t" . '<column name="'. $field .'">'. $value .'</column>' . "\n";
            }
            $result .= "</rule>\n";
        }
        $result .= "</rules>\n";

        $xml = new SimpleXMLElement($result);

        if ($filename == null) {
            $filename = 'PSN_rules_export_'. date('Y-m-d_H_i_s');
        }
        $filename .= '.xml';

        header('Content-disposition: attachment; filename="'. $filename .'"');
        header('Content-type: "text/xml"; charset="utf8"');
        echo $xml->asXML();
        exit;
    }

    /**
     * @param array $rules
     */
    protected function _bulkDelete(array $rules)
    {
        foreach($rules as $ruleId) {
            $rule = Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->find_one((int)$ruleId);
            $rule->delete();
        }
    }

    /**
     * @param array $rules
     */
    protected function _bulkDeactivate(array $rules)
    {
        foreach($rules as $ruleId) {
            $rule = Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->find_one((int)$ruleId);
            $rule->active = 0;
            $rule->save();
        }
    }

    /**
     * @param array $rules
     */
    protected function _bulkActivate($rules)
    {
        foreach($rules as $ruleId) {
            $rule = Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->find_one((int)$ruleId);
            $rule->active = 1;
            $rule->save();
        }
    }

    /**
     * @param array $rules
     */
    protected function _bulkExport($rules)
    {
        $rules = array_map('intval', $rules);
        $result = Ifw_Wp_ORM_Model::factory('Psn_Model_Rule')->where_in('id', $rules)->find_array();

        $this->_export($result);
    }

    /**
     * @return string
     */
    protected function _getDefaultHelpText()
    {
        return sprintf(__('Please consider the documentation page <a href="%s" target="_blank">%s</a> for more information.', 'ifw'),
            'http://docs.ifeelweb.de/post-status-notifier/rules.html',
            __('Rules', 'psn'));
    }

    /**
     * @return string
     */
    protected function _getHelpSidebar()
    {
        $sidebar = '<p><b>' . __('For more information:', 'ifw') . '</b></p>';
        $sidebar .= sprintf('<p><a href="%s" target="_blank">' . __('Plugin homepage', 'ifw') . '</a></p>',
            $this->_pm->getEnv()->getHomepage());
        if (!empty($this->_pm->getConfig()->plugin->docUrl)) {
            $sidebar .= sprintf('<p><a href="%s" target="_blank">' . __('Documentation', 'ifw') . '</a></p>',
            $this->_pm->getConfig()->plugin->docUrl);
        }
        return $sidebar;
    }

    /**
     * @return string
     */
    protected function _getHelpTextPlaceholders()
    {
        $tpl = Ifw_Wp_Tpl::getInstance($this->_pm);

        $placholders = new Psn_Notification_Placeholders();
        $placholders->addPlaceholder('post_status_before')->addPlaceholder('post_status_after');

        $placholdersResult = $placholders->getDefaultPlaceholders();
        asort($placholdersResult);
        $placholdersDynamic = $placholders->getPlaceholders('dynamic');
        asort($placholdersDynamic);

        $context = array(
            'placeholders' => $placholdersResult,
            'placeholdersDynamic' => $placholdersDynamic,
            'placeholdersDynamicHelp' => __('These placeholders are unique to this WordPress installation. They use the names of custom categories and tags.', 'psn'),
            'langHeader' => __('List of placeholders available for notification subject and text', 'psn'),
            'langStatic' => __('Static placeholders', 'psn'),
            'langDynamic' => __('Dynamic placeholders', 'psn'),
            'langCustomFields' => __('Custom fields', 'psn'),
            'langCustomFields1' => __('To retrieve the contents of custom post fields use this placeholder', 'psn'),
            'langCustomFields2' => __('The * stands for the name of the custom field.<br>Example: If you have a custom post field "actors" you should call your placeholder <b>[post_custom_field-actors]</b>', 'psn'),
        );

        return $tpl->render('admin_help_placeholders.html.twig', $context);
    }
}