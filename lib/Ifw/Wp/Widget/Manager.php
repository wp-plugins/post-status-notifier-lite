<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Widget manager
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
class Ifw_Wp_Widget_Manager
{
    /**
     * Instance store
     * @var array
     */
    public static $_instances = array();
    
    /**
     * The directory of the plugin's widget class(es)
     * @var string
     */
    protected $_widgetDir;
    
    /**
     * The plugin's abbreviation
     * @var string
     */
    protected $_abbr;


    /**
     * Retrieves singleton Ifw_Wp_Widget_Manager object
     *
     * @param Ifw_Wp_Plugin_Manager $pm
     * @internal param string $widget_dir
     * @return Ifw_Wp_Widget_Manager
     */
    public static function getInstance(Ifw_Wp_Plugin_Manager $pm)
    {
        $widget_dir = self::getWidgetDir($pm);
        
        if (!isset(self::$_instances[$widget_dir])) {
            self::$_instances[$widget_dir] = new self($widget_dir, $pm->getAbbr());
        }
    
        return self::$_instances[$widget_dir];
    }

    /**
     * @param string $widget_dir
     * @param $abbr
     * @throws Ifw_Wp_Widget_Exception
     */
    protected function __construct ($widget_dir, $abbr)
    {
        if (!is_dir($widget_dir)) {
            throw new Ifw_Wp_Widget_Exception('Invalid widget directory');
        }
        $this->_widgetDir = $widget_dir;
        $this->_abbr = $abbr;
    }
    
    /**
     * Checks if plugin has widgets
     * 
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return boolean
     */
    public static function hasWidgets(Ifw_Wp_Plugin_Manager $pm)
    {
        $widget_dir = self::getWidgetDir($pm);
        
        return is_dir($widget_dir) && count(array_diff(scandir($widget_dir), array('.', '..')) > 0);
    }
    
    /**
     * Retrieves the direcotry where the plugin's widget class(es) reside
     * 
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return string
     */
    public static function getWidgetDir(Ifw_Wp_Plugin_Manager $pm)
    {
        $widget_dir = $pm->getPathinfo()->getRootAdmin() . 'widget' . DIRECTORY_SEPARATOR;
        return $widget_dir;
    }
    
    /**
     * Autoloads widgets
     */
    public function autoload()
    {
        foreach (scandir($this->_widgetDir) as $file) {
            if (!strstr($file, '.php')) {
                continue;
            }
            
            $widget_classname = $this->_abbr . '_Widget_' . array_shift(explode('.', $file));
            if (!class_exists($widget_classname)) {
                require_once $this->_widgetDir . $file;
            }
          
            if (is_subclass_of($widget_classname, 'Ifw_Wp_Widget_Abstract')) {
                $this->register($widget_classname);
            }
        }
    }

    /**
     * Registers a widget
     *
     * @param $widget_classname
     * @return bool|void
     * @internal param string $widget_class_name
     */
    public function register($widget_classname)
    {
        return Ifw_Wp_Proxy_Action::add('widgets_init', create_function('', 'return register_widget("'. $widget_classname .'");'));
    }

    /**
     * Checks for widget menu access
     *
     * @return boolean
     */
    public static function isAccess()
    {
        $requestInfo = pathinfo($_SERVER['REQUEST_URI']);
        if ($requestInfo['filename'] == 'widgets') {
            return true;
        }
        return false;
    }
}
