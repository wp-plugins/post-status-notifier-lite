<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Template engine factory/singleton to retrieve twig environment
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
class Ifw_Wp_Tpl
{
    /**
     * Instance store
     * @var array
     */
    protected static $_instances = array();

    /**
     * 
     * @var unknown_type
     */
    protected static $_stringLoaderInstance;



    /**
     * Retrieves a Twig environment with filesystem loader
     *
     * @param Ifw_Wp_Plugin_Manager $pm
     * @param array $options
     * @internal param string $loader
     * @return IfwTwig_Environment
     */
    public static function getInstance(Ifw_Wp_Plugin_Manager $pm, $options=array())
    {
        return self::getFilesytemInstance($pm, $options);
    }

    /**
     *
     * @param array $options
     * @throws Ifw_Wp_Exception
     * @internal param \Ifw_Wp_Plugin_Manager $pm
     * @internal param string $loader
     * @internal param array $twigOptions
     * @return IfwTwig_Environment
     */
    public static function factory($options=array())
    {
        if (!isset($options['twig_loader']) || empty($options['twig_loader'])) {
            $options['twig_loader'] = 'Filesystem';
        }
        
        $twigOptions = array();
        if (isset($options['twig_options']) && is_array($options['twig_options'])) {
            $twigOptions = $options['twig_options'];
        }
       
        switch ($options['twig_loader']) {
            
            case 'String':
                
                $tpl = new IfwTwig_Environment(new IfwTwig_Loader_String(), $twigOptions);
                break;
                
            case 'Filesystem':
            default:
                if (!isset($options['plugin_manager']) || !($options['plugin_manager'] instanceof Ifw_Wp_Plugin_Manager)) {
                    throw new Ifw_Wp_Exception('Filesystem loader requires instance of Ifw_Wp_Plugin_Manager');
                }
                $pm = $options['plugin_manager'];
                $loader = new IfwTwig_Loader_Filesystem($pm->getPathinfo()->getRootTpl());
                $loader->addPath(dirname(__FILE__) . '/Tpl/built-in');
                $tpl = new IfwTwig_Environment($loader, $twigOptions);
        }

        $tpl->addGlobal('text', new Ifw_Wp_Tpl_Text());
        $tpl->addExtension(new Ifw_Wp_Tpl_Extension_DateLocale());
        $tpl->addExtension(new Ifw_Wp_Tpl_Extension_Text());
        return $tpl;
    }
    
    /**
     * Retrieves a Twig environment with string loader
     * 
     * 
     * @param array $twigOptions
     * @return IfwTwig_Environment
     */
    public static function getStringLoaderInstance($twigOptions=array())
    {
        if (self::$_stringLoaderInstance === null) {
            $options = array('twig_loader' => 'String');
            if (!empty($twigOptions) && is_array($twigOptions)) {
                $options['twig_options'] = $twigOptions;
            }
            self::$_stringLoaderInstance = self::factory($options);
        }
        return self::$_stringLoaderInstance;
    }

    /**
     * Retrieves singleton Ifw_Tpl object
     * obsolete, use self::getInstance instead
     *
     * @param Ifw_Wp_Plugin_Manager $pm
     * @param string $loader
     * @param array $options
     * @return IfwTwig_Environment
     */
    public static function getTwigInstance(Ifw_Wp_Plugin_Manager $pm, $loader='Filesystem', $options=array())
    {
        return self::getFilesytemInstance($pm, $options);
    }
    
    /**
     * Retrieves a Twig environment with filesystem loader
     * 
     * @param Ifw_Wp_Plugin_Manager $pm
     * @param array twig options
     * @return IfwTwig_Environment
     */    
    public static function getFilesytemInstance(Ifw_Wp_Plugin_Manager $pm, $twigOptions=array())
    {
        $options = array(
            'twig_loader' => 'Filesystem',
            'plugin_manager' => $pm
        );
        if (!empty($twigOptions) && is_array($twigOptions)) {
            $options['twig_options'] = $twigOptions;
        }
         
        if (!isset(self::$_instances[$pm->getAbbr()][$options['twig_loader']])) {
            self::$_instances[$pm->getAbbr()][$options['twig_loader']] = self::factory($options);
        }
        
        return self::$_instances[$pm->getAbbr()][$options['twig_loader']];
    }
    
    /**
     * Applies twig filters to string
     * 
     * @param string $string
     * @param string $filters
     * @param null|Ifw_Wp_Plugin_Logger $logger
     * @return string
     */
    public static function applyFilters($string, $filters, $logger=null)
    {
        if (!empty($filters)) {
            try {
                $tpl = self::getStringLoaderInstance();
                $string = $tpl->render('{{ value|'. $filters .' }}', array('value' => $string));
            } catch (Exception $e) {
                // invalid filter handling
                if ($logger instanceof Ifw_Wp_Plugin_Logger) {
                    $logger->err($e->getMessage());
                }
            }
        }
        
        return $string;
    }

    /**
     * Applies twig filters to string
     *
     * @param string $string
     * @param null|Ifw_Wp_Plugin_Logger $logger
     * @internal param string $filters
     * @return string
     */
    public static function renderString($string, $logger=null)
    {
        if (!empty($string)) {
            try {
                $tpl = self::getStringLoaderInstance();
                $string = $tpl->render($string);
            } catch (Exception $e) {
                // invalid filter handling
                if ($logger instanceof Ifw_Wp_Plugin_Logger) {
                    $logger->err($e->getMessage());
                }
            }
        }

        return $string;
    }
}
