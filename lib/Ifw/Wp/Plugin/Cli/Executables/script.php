<?php
if (!class_exists('Ifw_Wp_Plugin_Cli_Loader')) {
    require_once dirname(__FILE__) . '/../Loader.php';
    require_once dirname(__FILE__) . '/../../../Exception.php';
    require_once dirname(__FILE__) . '/../../Exception.php';
    require_once dirname(__FILE__) . '/../Exception.php';
}

$localScriptPath = $_SERVER['argv'][1];
array_splice($_SERVER['argv'], 1, 1);

try {
    Ifw_Wp_Plugin_Cli_Loader::init($localScriptPath);
} catch (Ifw_Wp_Plugin_Cli_Exception $e) {
    echo 'Error while script initialization: ' . $e->getMessage();
} catch (Exception $e) {
    echo 'General error: ' . $e->getMessage();
}
