<?php
/*
 * This is where it all begins
 */

define('ROOT', __DIR__ .'/');

mb_internal_encoding('UTF-8');

// autoload classes
spl_autoload_register(function($className) {
	require (ROOT . str_replace('\\', '/', $className) .'.php');
});

new \UT\Hans\AutoMicrosite\Request();

?>