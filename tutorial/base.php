<?php

// Setup the include path
$currentIncludePath = ini_get('include_path');
ini_set('include_path', '.' . PATH_SEPARATOR . dirname(dirname(__FILE__)) . '/lib' .  PATH_SEPARATOR . $currentIncludePath);

// Display errors
ini_set('display_errors', 1);

/**
 * Autoloader for PHP classes.
 *
 * @param string $classname
 */
function __autoload($classname) {
	$classfile = preg_replace('/_/', '/', $classname) . '.php';
	
	require_once $classfile;
}

// Include configuration
require_once 'config.php';

?>
