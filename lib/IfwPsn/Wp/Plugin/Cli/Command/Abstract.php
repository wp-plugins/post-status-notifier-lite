<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Abstract cli command
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  IfwPsn_Wp
 */
abstract class IfwPsn_Wp_Plugin_Cli_Command_Abstract
{
    /**
     * @var IfwPsn_Wp_Plugin_Manager
     */
    protected $_pm;
    
    /**
     * @var string
     */
    protected $_command;
    
    /**
     * @var array
     */
    protected $_params;
    
    /**
     * @var array
     */
    protected $_supportedParams = array();
    
    /**
     * @var string
     */
    protected $_executable = 'script';
    
    
    
    /**
     *
     */
    public function __construct ($command, $params, IfwPsn_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
        $this->_command = $command;
        $this->_params = $params;
    
        $this->_initParams();
    }

    /**
     * 
     */
    protected function _initParams()
    {
        $this->_params = $this->_prepareParams($this->_params);
        $this->_supportedParams = $this->_fetchSupportedParams();
        $this->_validateSupportedParams();
    }
    
    /**
     * May be overwritten by concrete command
     * 
     * @return array
     */
    protected function _fetchSupportedParams()
    {
        return array();
    }
    
    /**
     * @throws Coach_Cli_Exception_MissingOperand
     */
    protected function _validateSupportedParams()
    {
        $requiredParams = $this->_getSupportedRequiredParams();
    
        foreach ($requiredParams as $reqParam) {
    
            if (!isset($this->_params[$reqParam['name']]) && !isset($this->_params[$reqParam['shortName']])) {
                throw new IfwPsn_Wp_Plugin_Cli_Command_Exception_MissingOperand($this->_fetchUsage());
            }
        }
    }
    
    /**
     *
     */
    protected function _getSupportedRequiredParams()
    {
        $params = array();
        foreach ($this->_supportedParams as $param) {
            if ($param['required'] == true) {
                $params[] = $param;
            }
        }
        return $params;
    }
    
    /**
     * @return multitype:unknown
     */
    protected function _getSupportedOptionalParams()
    {
        $params = array();
        foreach ($this->_supportedParams as $param) {
            if ($param['required'] == false) {
                $params[] = $param;
            }
        }
        return $params;
    }
    
    /**
     *
     */
    protected function _getDefaultUsage()
    {
        $usage = 'Usage: '. $this->_executable . ' ' . $this->_command . ' ';
    
        $usageParams = array();
        $usageParamsDescription = array();
    
        $supportedParams = array_merge($this->_getSupportedRequiredParams(), $this->_getSupportedOptionalParams());
    
        foreach ($supportedParams as $param) {
            $paramUsage = $param['usage'];
            if ($param['required'] == false) {
                $paramUsage = '[' . $paramUsage . ']';
            }
            $usageParams[] = $paramUsage;
    
            if (!empty($param['description'])) {
                $description = '--' . $param['name'];
                if (!empty($param['shortName'])) {
                    $description = '-' . $param['shortName'] . ', ' . $description;
                }
                $description .= ': ' . $param['description'];
                if (($param['required'] == false)) {
                    $description .= ' (optional)';
                }
    
                $usageParamsDescription[] = $description;
            }
        }
    
        $usage .= implode(' ', $usageParams);
    
        if (count($usageParamsDescription) > 0) {
            $usage .= PHP_EOL . PHP_EOL . 'Options:' . PHP_EOL;
            $usage .= implode(PHP_EOL . PHP_EOL, $usageParamsDescription);
        }
    
        return $usage;
    }
    
    /**
     * @param string $paramname
     * @return bool
     */
    protected function _getParam($paramname)
    {
        $result = false;
    
        if (is_array($this->_supportedParams)) {
    
            foreach ($this->_supportedParams as $param) {
    
                if ($paramname == $param['name'] || $paramname == $param['shortName']) {
                    if (isset($this->_params[$param['name']])) {
                        $result = $this->_params[$param['name']];
                    } elseif (isset($this->_params[$param['shortName']])) {
                        $result = $this->_params[$param['shortName']];
                    }
                    break;
                }
            }
        }
    
        return $result;
    }
    
    /**
     * Retrieves the usage output, may be overwritten to customize by command
     */
    protected function _fetchUsage()
    {
        return $this->_getDefaultUsage();
    }
    
    /**
     * @param $output
     */
    public function output($output)
    {
        echo $output;
        echo PHP_EOL;
    }
    
    /**
     * @param $output
     */
    public function outputInline($output)
    {
        echo $output;
    }
    
    /**
     * Prepares command line parameters for use
     * @param array $params
     * @return array
     */
    protected function _prepareParams($params)
    {
        $newparams = array();
    
        foreach ($params as $param) {
    
            if (!strstr($param, '=')) {
                $newparams[] = $param;
            } else {
                $p = explode('=', $param);
    
                $paramKey = $p[0];
                $paramValue = $p[1];
    
                if (stripos($paramKey, '--') === 0) {
                    $paramKey = substr($paramKey, 2);
                } elseif (stripos($paramKey, '-') === 0) {
                    $paramKey = substr($paramKey, 1);
                }
    
                if (stripos($paramValue, '"') === 0) {
                    $paramValue = substr($paramValue, 1);
                }
                if (strrpos($paramValue, '"') === (strlen($paramValue)-1)) {
                    $paramValue = substr($paramValue, 0, -1);
                }
    
                if ($paramValue == 'true') {
                    $newparams[$paramKey] = true;
                } else if ($paramValue == 'false') {
                    $newparams[$paramKey] = false;
                } else if (preg_match('/^[0-9]*$/', $paramValue)) {
                    $newparams[$paramKey] = (int)$paramValue;
                } else {
                    $newparams[$paramKey] = $paramValue;
                }
            }
        }
    
        return $newparams;
    }

    /**
     * @return the $_executable
     */
    public function getExecutable()
    {
        return $this->_executable;
    }

    /**
     * @param $executable
     * @internal param string $_executable
     */
    public function setExecutable($executable)
    {
        $this->_executable = $executable;
    }
    
    /**
     * Executes the command
     */
    public abstract function execute();

}
