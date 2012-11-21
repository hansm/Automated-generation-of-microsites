<?php
/*
 * This is where it all begins
 */

define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);

mb_internal_encoding('UTF-8');

date_default_timezone_set('Europe/Tallinn');

// Autoload classes
spl_autoload_register(function($className) {
	require (ROOT . str_replace('\\', DIRECTORY_SEPARATOR, $className) .'.php');
});

// Convert errors to ErrorExceptions
function exception_error_handler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler('exception_error_handler');

new \UT\Hans\AutoMicrosite\Request();

exit();




$template = new \UT\Hans\AutoMicrosite\Template\MicrodataTemplate(
	'http://localhost/Automated-generation-of-microsites/AutoMicrosite/Templates/Simple.html'
	);

$template->setTitle('My Cool Site');
$template->appendToHead($openAjaxHub);

$templateSlots = $template->getSlots();

$template->setSlot($templateSlots->item(0), '<span>test</span>');

echo $template->getHtml();

?>