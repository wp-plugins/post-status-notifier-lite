<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * RssFeed Metabox
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  IfwPsn_Wp_Plugin_Admin_Menu_Metabox
 */
require_once dirname(__FILE__) . '/Ajax.php';

abstract class IfwPsn_Wp_Plugin_Metabox_RssFeed extends IfwPsn_Wp_Plugin_Metabox_Ajax
{
    /**
     * The rss feed url
     * @var string
     */
    protected $_feedUrl;
    
    /**
     * How many items should be displayed
     * @var int
     */
    protected $_feedItems = 3;
    
    /**
     * The template file used for rendering the items
     * @var string
     */
    protected $_feedItemsTpl = 'metabox_rss_default.html.twig';
    
    /**
     * @var SimplePie
     */
    protected $_rss;
    
    
    
    /**
     * 
     * @param IfwPsn_Wp_Plugin_Manager $pm
     */
    public function __construct (IfwPsn_Wp_Plugin_Manager $pm)
    {
        parent::__construct($pm);
        
        $this->_feedUrl = $this->_initFeedUrl();
    }
    
    /**
     * 
     * @throws Exception
     * @return SimplePie
     */
    protected function _fetchFeed()
    {
        if ($this->_rss == null) {
            $this->_rss = fetch_feed($this->_feedUrl);
            
            if (!($this->_rss instanceof SimplePie)) {
                throw new Exception('Invalid feed result');
            }
        }
        
        return $this->_rss;
    }

    /**
     *
     * @param unknown_type $end
     * @param int|\unknown_type $start
     * @return multitype:
     */
    protected function _getFeedItems($end, $start=0)
    {
        $rss = $this->_fetchFeed();
        return $rss->get_items($start, $end);
    }
    
    /**
     * 
     * @return IfwPsn_Wp_Ajax_Response
     */
    public function getAjaxResponse()
    {
        try {
            $rssItems = $this->_getFeedItems($this->_feedItems);
    
            $tpl = IfwPsn_Wp_Tpl::getFilesytemInstance($this->_pm);
    
            $success = true;
            $html = $tpl->render($this->_feedItemsTpl, array('items' => $this->_prepareItems($rssItems)));
    
        } catch (Exception $e) {
            $success = false;
            $html = sprintf(__('Error while loading newsfeed: %s', 'ifw'), $this->_feedUrl);
        }
    
        return new IfwPsn_Wp_Ajax_Response($success, $html);
    }
    
    /**
     * Prepares feed item data
     * 
     * @param array $items
     * @return array
     */
    protected function _prepareItems($items)
    {
        $result = array();
            
        foreach ($items as $item) {
            
            $item_tmp = array();
            
            $item_tmp['url'] = esc_url($item->get_link());
            
            $title = esc_attr($item->get_title());
            
            if (empty($title)) {
                $title = __('Untitled');
            }
            $item_tmp['title'] = $title;
        
            $desc = str_replace(array("\n", "\r"), ' ', esc_attr(strip_tags(@html_entity_decode($item->get_description(), ENT_QUOTES, get_option('blog_charset')))));
            $desc = wp_html_excerpt($desc, 360);
            
            if (strstr($desc, 'Continue reading →')) {
                $desc = str_replace('Continue reading →', '<a href="'. $item_tmp['url'] .'" target="_blank">Continue reading →</a>', $desc);
            }
            $item_tmp['desc'] = $desc;
        
            $date = $item->get_date();
            $diff = '';
        
            if ($date) {
                 
                $diff = human_time_diff(strtotime($date, time()));
                $date_stamp = strtotime($date);
                if ($date_stamp) {
                    $date = '<span class="rss-date">' . date_i18n(get_option('date_format'), $date_stamp) . '</span>';
                } else {
                    $date = '';
                }
            }
            
            $item_tmp['date'] = $date;
            $item_tmp['diff'] = $diff;
            
            $result[] = $item_tmp;
        }
        
        return $result;
    }

    /**
     * Sets the feed url
     */
    abstract protected function _initFeedUrl();
}
