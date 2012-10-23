<?php
/*
 * This is where it all begins
 */

define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);

mb_internal_encoding('UTF-8');

date_default_timezone_set('Europe/Tallinn');

// autoload classes
spl_autoload_register(function($className) {
	require (ROOT . str_replace('\\', DIRECTORY_SEPARATOR, $className) .'.php');
});

header( "content-type: application/xml; charset=UTF-8" );

$transformer = new \UT\Hans\AutoMicrosite\RuleMl\OpenAjaxToRuleMl();
$result = $transformer->transformFile('Widgets/Map/Map.oam.xml', 101);
//$result = $transformer->transformFile('data/data.oam.xml', 101);
print_r($result->getString());

?>