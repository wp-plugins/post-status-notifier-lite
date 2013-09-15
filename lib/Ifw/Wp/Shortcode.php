<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Regex parser
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
abstract class Ifw_Wp_Shortcode implements Ifw_Wp_Interface_Loggable
{
    /**
     * @var string
     */
    protected $_shortcode;
    
    /**
     * @var Ifw_Wp_Plugin_Logger
     */
    protected $_logger;

    
    
    /**
     * @param string $shortcode
     * @param array $options
     */
    public function __construct($shortcode, array $options = array())
    {
        $this->_shortcode = $shortcode;
        
        $this->_init($options);
    }

    /**
     * @param array $options
     */
    protected function _init(array $options)
    {
        add_shortcode($this->_shortcode, array($this, 'handle'));
        
        // add default filters
        Ifw_Wp_Proxy_Filter::addWidgetText('do_shortcode');
        Ifw_Wp_Proxy_Filter::addTheExcerpt('do_shortcode');
        Ifw_Wp_Proxy_Filter::addTheExcerptFeed('do_shortcode');
        Ifw_Wp_Proxy_Filter::addTheExcerptRss('do_shortcode');
        Ifw_Wp_Proxy_Filter::addTheContentFeed('do_shortcode');
        Ifw_Wp_Proxy_Filter::addTheContentRss('do_shortcode');
        
        // add additional custom filters
        if (isset($options['filters']) && is_array($options['filters'])) {
            foreach ($options['filters'] as $filter) {
                Ifw_Wp_Proxy_Filter::add($filter, 'do_shortcode');
            }
        }
    }
    
    /**
     * 
     * @param array $options
     * @param string $content
     * @param string $code
     * @return string
     */
    public function handle($options, $content='', $code='')
    {
        $replacement = $this->_getReplacement($options, $content, $code);
         
        $replacement = $this->_applyFilters($replacement, $options);
        
        return $replacement;
    }

    /**
     * Must be overwritten by concrete class implementation
     *
     * @param array $options
     * @param string $content
     * @param string $code
     * @return string the replacement
     */
    abstract protected function _getReplacement($options, $content='', $code='');
    
    /**
     * Applies filters to the replacement string
     *
     * @param string $replacement
     * @param array $options
     * @return string
     */
    protected function _applyFilters($replacement, $options)
    {
        if (!empty($options['filters'])) {
            $replacement = Ifw_Wp_Tpl::applyFilters($replacement, $options['filters'], $this->_logger);
        }
        
        return $replacement;
    }

    /**
     * Set logger
     * @param Ifw_Wp_Plugin_Logger $logger
     */
    public function setLogger(Ifw_Wp_Plugin_Logger $logger)
    {
        $this->_logger = $logger;
    }
    
    /**
     * Get logger
     * @return Ifw_Wp_Plugin_Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}
