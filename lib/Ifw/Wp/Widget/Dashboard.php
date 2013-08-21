<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
abstract class Ifw_Wp_Widget_Dashboard
{
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * Unique ID
     * @var string
     */
    protected $_id;

    /**
     * Dashboard widget's title
     * @var string
     */
    protected $_title = '';



    /**
     * @param $id
     * @param null $title
     */
    public function __construct(Ifw_Wp_Plugin_Manager $pm, $id, $title = null)
    {
        $this->_pm = $pm;
        $this->_id = $id;

        if ($title != null) {
            $this->_title = $title;
        }

        $this->_init();
    }

    protected function _init()
    {
        Ifw_Wp_Proxy_Action::addWpDashboardSetup(array($this, 'add'));
    }

    public function add()
    {
        Ifw_Wp_Plugin_Admin_Skin::loadSkin($this->_pm);
        wp_add_dashboard_widget($this->_id, $this->_title, array($this, 'render'));
    }

    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * Renders the dashboard widget's output
     * @return mixed
     */
    public abstract function render();

    /**
     * Handles dashboard widget's interaction like form submission
     * @return mixed
     */
    public abstract function handle();
}
