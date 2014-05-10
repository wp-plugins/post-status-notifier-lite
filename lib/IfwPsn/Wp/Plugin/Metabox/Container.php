<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Metabox container
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  IfwPsn_Wp_Plugin_Admin_Menu_Metabox
 */
class IfwPsn_Wp_Plugin_Metabox_Container
{
    /**
     * @var string
     */
    protected $_screen;
    
    /**
     * @var string
     */
    protected $_context = 'advanced';
    
    /**
     * @var array
     */
    protected $_metaboxes = array();
    
    /**
     * @param string $screen
     * @param string $context
     */
    function __construct ($screen, $context = null)
    {
        $this->_screen = $screen;
        if ($context != null) {
            $this->_context = $context;
        }
    }
    
    /**
     * Adds a metabox to a container
     * 
     * @param IfwPsn_Wp_Plugin_Metabox_Abstract $metabox
     */
    public function addMetabox(IfwPsn_Wp_Plugin_Metabox_Abstract $metabox)
    {
        if ($metabox instanceof IfwPsn_Wp_Plugin_Metabox_Abstract) {
            $this->_metaboxes[] = $metabox;
            
            add_meta_box(
                $metabox->getId(),
                $metabox->getTitle(),
                array($metabox, 'render'),
                $this->_screen,
                $this->_context,
                $metabox->getPriority()
            ); 
        }
    }
    
    /**
     * Renders the container
     */
    public function render()
    {
        do_meta_boxes($this->_screen, $this->_context, '');
    }
}
