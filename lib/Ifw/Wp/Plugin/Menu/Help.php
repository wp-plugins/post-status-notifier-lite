<?php
/**
 * ifeelweb.de Wordpress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Admin Menu Contextual Help
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) ifeelweb.de
 * @version  $Id$
 * @package  Ifw_Wp_Plugin_Admin
 */
class Ifw_Wp_Plugin_Menu_Help
{
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;
    
    /**
     * @var string
     */
    protected $_title;
    
    /**
     * @var string
     */
    protected $_help;
    
    /**
     * @var string
     */
    protected $_sidebar;

    
    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public function __construct(Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
    }
    
    /**
     * Loads the appropriate action for adding contextual help
     */
    public function load()
    {
        if (Ifw_Wp_Proxy_Blog::isMinimumVersion('3.3')) {
            // since 3.3 use the add_help_method on the screen object
            Ifw_Wp_Proxy_Action::addAdminHead(array($this, 'addHelpTab'));
        } else {
            // before 3.3 use the contextual_help action
            Ifw_Wp_Proxy_Action::add('contextual_help', array($this, 'getContextualHelp'), 10, 3);
        }
    }
    
    /**
     * Callback for WP >= 3.3
     * @since WP 3.3
     */
    public function addHelpTab() 
    {
        $screen = Ifw_Wp_Proxy_Screen::getCurrent();

        $help = array(
            'id'=> 1,
            'title'=> $this->_title,
            'content'=> sprintf('<div class="ifw-help-tab-content">%s</div>', $this->_help)
        );

        Ifw_Wp_Proxy_Screen::addHelpTab($help);

        if (!empty($this->_sidebar)) {
            Ifw_Wp_Proxy_Screen::setHelpSidebar($this->_sidebar);
        }
    }
    
    /**
     * Callback for WP < 3.3
     * 
     * @param string $contextual_help
     * @param string $screen_id
     * @param unknown_type $screen
     * @return string
     */    
    public function getContextualHelp($contextual_help, $screen_id, $screen)
    {
        return $this->_help;
    }
    
    /**
     * @param string $title
     * @return Ifw_Wp_Plugin_Menu_Help
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * @param string $help
     * @return Ifw_Wp_Plugin_Menu_Help
     */
    public function setHelp($help)
    {
        $this->_help = $help;
        return $this;
    }

    /**
     * @param $sidebar
     * @return \Ifw_Wp_Plugin_Menu_Help
     * @internal param string $_sidebar
     */
    public function setSidebar($sidebar)
    {
        $this->_sidebar = $sidebar;
        return $this;
    }
}
