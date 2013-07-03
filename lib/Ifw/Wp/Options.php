<?php
/**
 * ifeelweb.de Wordpress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Options
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
class Ifw_Wp_Options
{
    /**
     * @var array
     */
    public static $_instances = array();

    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var string
     */
    protected $_pageId;

    /**
     * @var string
     */
    protected $_sectionPrefix;

    /**
     * @var string
     */
    protected $_fieldPrefix;

    /**
     * @var array
     */
    protected $_sections = array();

    /**
     * @var int
     */
    protected $_addedFields = 0;


    /**
     * Retrieves singleton object
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return Ifw_Wp_Options
     */
    public static function getInstance(Ifw_Wp_Plugin_Manager $pm)
    {
        if (!isset(self::$_instances[$pm->getAbbr()])) {
            self::$_instances[$pm->getAbbr()] = new self($pm);
        }
        return self::$_instances[$pm->getAbbr()];
    }

    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public function __construct(Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
        $this->_pageId = $pm->getAbbrLower() . '_options';
        $this->_sectionPrefix = $pm->getAbbrLower() . '_options_section_';
        $this->_fieldPrefix = $pm->getAbbrLower() . '_option_';
    }

    public function init()
    {
        Ifw_Wp_Proxy_Action::addAdminInit(array($this, 'register'));
    }

    /**
     * Loads the default general options section and triggers an action
     */
    public function load()
    {
        if ($this->_pm->isExactAdminAccess() ||
            (isset($_POST['option_page']) && $_POST['option_page'] == $this->_pageId)) {
            // init the option objects only if it is a exact plugin admin page access or save request
            $generalOptions = new Ifw_Wp_Options_Section('general', __('General Options', 'ifw'));
            Ifw_Wp_Proxy_Action::doAction($this->_pm->getAbbrLower() . '_general_options_init', $generalOptions);
            $this->addSection($generalOptions);

            $externalOptions = new Ifw_Wp_Options_Section('external', '');
            Ifw_Wp_Proxy_Action::doAction($this->_pm->getAbbrLower() . '_external_options_init', $externalOptions);
            $this->addSection($externalOptions);
        }
    }

    /**
     * @param Ifw_Wp_Options_Section $section
     * @param int $priority
     */
    public function addSection(Ifw_Wp_Options_Section $section, $priority = 10)
    {
        $this->_sections[$priority][uniqid()] = $section;
    }

    /**
     * Callback for admin_init
     */
    public function register()
    {
        ksort($this->_sections);

        /**
         * @var $section Ifw_Wp_Options_Section
         */
        foreach ($this->_sections as $priority) {
            foreach ($priority as $section) {

                if (!$section->hasFields()) {
                    continue;
                }

                add_settings_section(
                    $this->_sectionPrefix . $section->getId(), // section id
                    $section->getLabel(), // section label
                    array($section, 'render'), // callback to render the section's description
                    $this->_pageId // options page id on which to add this section
                );

                /**
                 * @var $field Ifw_Wp_Options_Field
                 */
                foreach ($section->getFields() as $field) {

                    add_settings_field(
                        $this->_fieldPrefix . $field->getId(), // field id
                        $field->getLabel(), // field label
                        array($field, 'render'), // method to render the field
                        $this->_pageId, // page id
                        $this->_sectionPrefix . $section->getId(), // section id
                        array($this) // passed to the render method of the field
                    );

                    $this->_addedFields++;
                }

                register_setting($this->_pageId, $this->_pageId);
            }
        }
    }

    /**
     * Renders the options form
     */
    public function render()
    {
        if ($this->_addedFields === 0):
            echo '<p>' . __('No options available.', 'ifw') . '</p>';
        else:
        ?>
        <form method="post" action="options.php">
            <?php settings_fields($this->_pageId); ?>
            <?php do_settings_sections($this->_pageId); ?>
            <?php submit_button(); ?>
        </form>
        <?php
        endif;
    }

    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return $this->_fieldPrefix;
    }

    /**
     * @return string
     */
    public function getPageId()
    {
        return $this->_pageId;
    }

    /**
     * @return string
     */
    public function getSectionPrefix()
    {
        return $this->_sectionPrefix;
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasOption($id)
    {
        $options = Ifw_Wp_Proxy::getOption($this->_pageId);

        return isset($options[$this->getOptionRealId($id)]);
    }

    /**
     * @param $id
     * @return null
     */
    public function getOption($id)
    {
        $result = null;

        if ($this->hasOption($id)) {
            $options = Ifw_Wp_Proxy::getOption($this->_pageId);
            $result = $options[$this->getOptionRealId($id)];
        }

        return $result;
    }

    /**
     * @param $id
     * @return string
     */
    public function getOptionRealId($id)
    {
        return $this->_fieldPrefix . $id;
    }

    /**
     * Retrieves all options
     */
    public function getAll()
    {
        return Ifw_Wp_Proxy::getOption($this->_pageId);
    }

    /**
     * Deletes all options
     */
    public function reset()
    {
        Ifw_Wp_Proxy::deleteOption($this->_pageId);
    }
}
