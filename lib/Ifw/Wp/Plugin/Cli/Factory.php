<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Cli command factory
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Wp_Plugin_Cli_Factory
{
    protected function __construct()
    {
    }

    /**
     * Return the command class
     *
     * @param string $command
     * @param array $args
     * @param Ifw_Wp_Plugin_Manager $pm
     * @throws Ifw_Wp_Plugin_Cli_Factory_Exception
     * @return Ifw_Wp_Cli_Command_Abstract
     */
    public static function getCommand($command, $args, Ifw_Wp_Plugin_Manager $pm)
    {
        $commandPath = Ifw_Wp_Autoloader::getClassPath($command);
    
        if ($commandPath == false) {
    
            throw new Ifw_Wp_Plugin_Cli_Factory_Exception('Unkown command: '. $command);
    
        } elseif (get_parent_class($command) != 'Ifw_Wp_Plugin_Cli_Command_Abstract') {
    
            throw new Ifw_Wp_Plugin_Cli_Factory_Exception('Command class must extend Ifw_Wp_Plugin_Cli_Command_Abstract');
    
        } else {
    
            return new $command($command, $args, $pm);
        }
    }
}
