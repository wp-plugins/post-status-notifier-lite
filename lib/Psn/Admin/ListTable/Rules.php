<?php
/**
 *
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) 2012-2013 ifeelweb.de
 * @package     Psn_Admin
 */
class Psn_Admin_ListTable_Rules extends Ifw_Wp_Plugin_ListTable_Abstract
{
    /**
     *
     */
    public function __construct(Ifw_Wp_Plugin_Manager $pm, $options = array())
    {
        $args = array('singular' => 'Rule', 'plural' => 'Rules');
        if (!empty($options)) {
            $args = array_merge($args, $options);
        }
        $data = new Psn_Admin_ListTable_Data_Rules();

        parent::__construct($args, $data, $pm);

        Ifw_Wp_Proxy_Action::add($this->_wpActionPrefix . 'after_display', array($this, 'afterDisplay'));
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'rules';
    }

    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_ListTable_Abstract::getColumns()
     */
    public function getColumns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => __('Rule name', 'psn'),
            'posttype' => __('Post type', 'psn'),
            'status_before' => __('Status before', 'psn'),
            'status_after' => __('Status after', 'psn'),
            'active' => __('Active', 'psn'),
        );

        if ($this->isMetaboxEmbedded()) {
            unset($columns['cb']);
        }

        return $columns;
    }

    /** (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_ListTable_Data_Interface::getSortableColumns()
     */
    public function getSortableColumns()
    {
        return $sortable_columns = array(
            'name' => array('name', true),
            'posttype' => array('posttype', false),
            'status_before' => array('status_before', false),
            'status_after' => array('status_after', false),
            'active' => array('active', false),
        );
    }

    /**
     * Custom column handling for active state
     *
     * @param $item
     * @return string
     */
    public function getColumnActive($item)
    {
        $format = '<img src="%sicons/%s.png" />';

        $skinUrl = $this->_pm->getEnv()->getSkinUrl();
        $icon = $item['active'] == '1' ? 'true' : 'false';

        return sprintf($format, $skinUrl, $icon);
    }

    /**
     * @param $items
     * @internal param $item
     * @return string
     */
    public function getColumnPosttype($items)
    {
        $result = '';
        $posttype = $items['posttype'];

        switch($posttype) {
            case 'all':
                $result = __('all types', 'psn');
                break;
            default:
                $result = Ifw_Wp_Proxy_Post::getTypeLabel($posttype);
        }

        return $result;
    }

    /**
     * @param $items
     * @internal param $item
     * @return string
     */
    public function getColumnStatusBefore($items)
    {
        return $this->_getStatusLabel($items['status_before']);
    }

    /**
     * @param $items
     * @internal param $item
     * @return string
     */
    public function getColumnStatusAfter($items)
    {
        return $this->_getStatusLabel($items['status_after']);
    }

    /**
     * @param $status
     * @return string|void
     */
    protected function _getStatusLabel($status)
    {
        $result = '';
        if ($status == 'new') {
            $result = __('New', 'ifw');
        } elseif ($status == 'anything') {
            $result = __('anything', 'psn');
        } else {
            $result = Ifw_Wp_Proxy_Post::getStatusLabel($status);
        }
        return $result;
    }

    /**
     * Custom column handling for name
     *
     * @param unknown_type $item
     * @return string
     */
    public function getColumnName($item)
    {
        $result = $item['name'];

        if (!$this->isMetaboxEmbedded()) {
            //Build row actions
            $actions = array(
                'edit'   => sprintf('<a href="?page=%s&controller=rules&action=edit&id=%s">'. __('Edit', 'psn') .'</a>', $_REQUEST['page'], $item['id']),
                'delete' => sprintf('<a href="?page=%s&controller=rules&action=delete&id=%s" class="delConfirm">'. __('Delete', 'psn') .'</a>', $_REQUEST['page'], $item['id']),
            );

            //Return the title contents
            $result = sprintf('%1$s%2$s',
                /*$1%s*/ $item['name'],
                /*$2%s*/ $this->row_actions($actions)
            );
        }

        return $result;
    }

    public function getExtraControlsTop()
    {
        $this->search_box(__('Search'), 'name');
        $this->displayReloadButton();
    }

    /**
     * Renders the checkbox column (hard coded in class-wp-list-table.php)
     */
    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item['id']
        );
    }

    /**
     * @return array
     */
    public function get_bulk_actions()
    {
        $actions = array();

        if (!$this->isMetaboxEmbedded()) {
            $actions = array(
                'delete' => __('Delete'),
                'activate' => __('Activate', 'psn'),
                'deactivate' => __('Deactivate', 'psn'),
            );
        }

        return $actions;
    }

    public function process_bulk_action()
    {

    }

    /**
     * (non-PHPdoc)
     * @see WP_List_Table::display()
     */
    public function afterDisplay()
    {
        ?>
        <script type="text/javascript">
            jQuery(".delConfirm").click(function(e) {
                e.preventDefault();
                var targetUrl = jQuery(this).attr("href");

                if (confirm('<?php _e('Are you sure you want to do this?'); ?>')) {
                    document.location.href = targetUrl;
                }
            });
        </script>
        <?php
    }
}
