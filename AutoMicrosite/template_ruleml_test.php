<?php

define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);

mb_internal_encoding('UTF-8');

date_default_timezone_set('Europe/Tallinn');

// autoload classes
spl_autoload_register(function($className) {
	require (ROOT . str_replace('\\', DIRECTORY_SEPARATOR, $className) .'.php');
});

if (empty($_GET['noheader'])) header( "content-type: application/xml; charset=UTF-8" );
$template = new UT\Hans\AutoMicrosite\Template\MicrodataTemplate('Templates/Simple.html');
$ruleMl = $template->toRuleMl();
print_r($ruleMl->getString());

?>