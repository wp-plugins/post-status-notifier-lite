<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Cli loader
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */

class Ifw_Wp_Plugin_Cli_Loader
{
    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public static function load(Ifw_Wp_Plugin_Manager $pm)
    {
        if (!$pm->getEnv()->isCli()) {
            // die if not command line accessed
            die('Invalid access');
        }
        
        $args = $_SERVER['argv'];
        $command = $args[1];
        $args = array_slice($args, 2);
        $executable = 'script';
        
        if ($pm->getEnv()->isWindows()) {
            $executable .= '.bat';
        } else {
            $executable .= '.sh';
        }
        
        try {
            // try to execute a command
            $cliCommand = Ifw_Wp_Plugin_Cli_Factory::getCommand($command, $args, $pm);
            $cliCommand->setExecutable($executable);
            $cliCommand->execute();
        
        } catch (Ifw_Wp_Plugin_Cli_Factory_Exception $e) {
        
            echo 'Initialization error: ' . $e->getMessage();
        
        } catch (Ifw_Wp_Plugin_Cli_Command_Exception_MissingOperand $e) {
            // fetch MissingOperand exception
            echo $executable . ' ' . $command . ': missing operand';
            echo PHP_EOL;
            echo $e->getMessage();
        
        } catch (Ifw_Wp_Plugin_Cli_Exception $e) {
            // fetch generell cli exception
            echo $e->getMessage();
        }
    }

    /**
     * Trys to load wp-load.php
     *
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return bool
     */
    public static function init($path)
    {
        if (!is_dir($path)) {
            throw new Ifw_Wp_Plugin_Cli_Exception('Invalid path: ' . $path);
        }

        self::_loadWpEnvironment($path);
        $pm = self::_getPluginManager($path);

        self::load($pm);
    }

    /**
     * @param $path
     * @return bool
     * @throws Ifw_Wp_Plugin_Cli_Exception
     */
    protected static function _loadWpEnvironment($path)
    {
        $searchDir = $path;
        if ($searchDir[strlen($searchDir)-1] == DIRECTORY_SEPARATOR) {
            $searchDir = substr($searchDir, 0, -1);
        }

        $counter = 10;

        while ($counter > 0) {

            $loadPath = $searchDir . DIRECTORY_SEPARATOR . 'wp-load.php';

            if (file_exists($loadPath)) {

                define('WP_INSTALLING', true);
                require_once $loadPath;
                return true;
            }

            $searchDir = substr($searchDir, 0, strrpos($searchDir, DIRECTORY_SEPARATOR));
            $counter--;
        }

        throw new Ifw_Wp_Plugin_Cli_Exception('Could not load WP environment from ' . $path);
    }

    /**
     * @param $path
     * @return mixed
     * @throws Ifw_Wp_Plugin_Cli_Exception
     */
    public function _getPluginManager($path)
    {
        $pathParts = array_reverse(explode(DIRECTORY_SEPARATOR, $path));
        $plugin_name = $pathParts[1];

        $pluginRootFile = $path . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $plugin_name . '.php';

        if (!file_exists($pluginRootFile)) {
            throw new Ifw_Wp_Plugin_Cli_Exception('Could not load plugin root file ' . $pluginRootFile);
        }

        require_once $pluginRootFile;

        if (!isset($ifwPluginManager) or !is_a($ifwPluginManager, 'Ifw_Wp_Plugin_Manager')) {
            throw new Ifw_Wp_Plugin_Cli_Exception('Could not load PluginManager');
        }

        return $ifwPluginManager;
    }
}
