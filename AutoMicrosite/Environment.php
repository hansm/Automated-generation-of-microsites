<?php
/**
 * Set up PHP environment
 */

define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);

mb_internal_encoding('UTF-8');

date_default_timezone_set('Europe/Tallinn');

// Autoload classes
spl_autoload_register(function($className) {
	require ROOT . str_replace('\\', DIRECTORY_SEPARATOR, $className) .'.php';
});

// Convert errors to ErrorExceptions
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}, E_ALL^E_NOTICE);
