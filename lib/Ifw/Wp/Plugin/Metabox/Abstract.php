<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Metabox Abstract
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp_Plugin_Admin_Menu_Metabox
 */
abstract class Ifw_Wp_Plugin_Metabox_Abstract
{
    /**
     * @var string
     */
    protected $_id;
    
    /**
     * @var string
     */
    protected $_title;
    
    /**
     * @var string
     */
    protected $_priority;
    
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;
    
    
    
    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    function __construct(Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
        
        $this->_init();
    }

    /**
     * 
     */
    protected function _init()
    {
        $this->_id = $this->_initId();
        $this->_title = $this->_initTitle();
        $this->_priority = $this->_initPriority();

        $this->enqueueScripts();

        $this->init();   
    }
    
    /**
     * May be overwritten by subclasses
     */
    public function init()
    {}
    
    /**
     * Enqueues the required scripts to get the metaboxes working
     * Scripts get only enqueued once by WP internally
     */
    public function enqueueScripts()
    {
        Ifw_Wp_Proxy_Script::loadAdmin('common');
        Ifw_Wp_Proxy_Script::loadAdmin('wp-lists');
        Ifw_Wp_Proxy_Script::loadAdmin('postbox');
    }
    
    /**
     * @return the $_id
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return the $_title
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @return the $_priority
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Renders the metabox contents
     * @return mixed
     */
    abstract public function render();

    /**
     * Sets the metabox id
     * @return mixed
     */
    abstract protected function _initId();
    
    /**
     * Sets the metabox title
     * @return string title of the metabox
     */
    abstract protected function _initTitle();

    /**
     * Sets the metabox priority
     * @return mixed
     */
    abstract protected function _initPriority();
}