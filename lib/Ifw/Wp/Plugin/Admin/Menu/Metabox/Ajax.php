<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Ajax Metabox
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp_Plugin_Admin_Menu_Metabox
 */
abstract class Ifw_Wp_Plugin_Admin_Menu_Metabox_Ajax extends Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract
{
    /**
     * The name of the ajax action
     * @var string
     */
    protected $_ajaxAction;
    
    /**
     * @var Ifw_Wp_Ajax_Request_Abstract
     */
    protected $_ajaxRequest;

    
    
    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public function __construct (Ifw_Wp_Plugin_Manager $pm)
    {
        parent::__construct($pm);
        
        $this->_ajaxAction = $this->_initAjaxAction();
    }
    
    /**
     * Initializes the ajax request object
     */
    public function _initAjaxRequest()
    {
        $this->_ajaxRequest = new Ifw_Wp_Ajax_Request_Private($this->getAjaxAction());
        $this->_ajaxRequest->setCallback(array($this, 'getAjaxResponse'));
    }
    
    /**
     * Retrieves the ajax request object
     * 
     * @return Ifw_Wp_Ajax_Request_Private
     */
    public function getAjaxRequest()
    {
        if ($this->_ajaxRequest == null) {
            $this->_initAjaxRequest();
        }
        return $this->_ajaxRequest;
    }
        
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::render()
     */
    public function render()
    {
        ?>
        <div class="ifw_wp_metabox_loading"></div>
        <script type="text/javascript">
            //<![CDATA[
            function metabox_<?php echo $this->_id; ?>_reload() {

                jQuery('#<?php echo $this->_id; ?>').find('.inside').html('<div class="ifw_wp_metabox_loading"></div>');

                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                if (typeof ajaxurl == 'undefined') {
                    var ajaxurl = 'admin-ajax.php';
                }

                var data = {
                    action: '<?php echo $this->getAjaxAction(); ?>',
                    nonce: '<?php echo $this->getAjaxRequest()->getNonce(); ?>'
                };

                jQuery.getJSON(ajaxurl, data, function(response) {
                    jQuery('#<?php echo $this->_id; ?>').find('.inside').html(response.html);
                });
            }
            jQuery(document).ready(function($) {
                metabox_<?php echo $this->_id; ?>_reload();
            });
            //]]>
        </script>        
        <?php 
    }
        
    /**
     * @return the $_ajaxAction
     */
    public function getAjaxAction()
    {
        return $this->_ajaxAction;
    }
    
    /**
     * 
     * @return string
     */
    protected function _initAjaxAction()
    {
        return 'load-' . $this->_pm->getAbbrLower() . '-' . $this->getId();
    }

    /**
     * Retrieves the ajax response object
     * 
     * @return Ifw_Wp_Ajax_Response
     */
    abstract public function getAjaxResponse();
    
    /**
     * May be overwritten by subclass
     */
    public function initAjaxResponse()
    {}
}
