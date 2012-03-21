<?php
/*
 * This is where it all begins
 */

use UT\Hans\AutoMicrosite\Mashup;

define('ROOT', __DIR__ .'/');

mb_internal_encoding('UTF-8');

// autoload classes
spl_autoload_register(function($className) {
	require (ROOT . str_replace('\\', '/', $className) .'.php');
});

$mashup = new Mashup();
$mashup->setTitle('New mashup');

$mashup->loadWidget(ROOT .'Widgets/map1_oam.xml');

$mashup->positionWidgets();
$mashup->output();

?>