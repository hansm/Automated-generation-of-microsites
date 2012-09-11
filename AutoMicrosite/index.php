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

//new \UT\Hans\AutoMicrosite\Request();

$openAjaxHub = '<script type="text/javascript" src="js/OpenAjaxManagedHub-all.js"></script>
<script type="text/javascript">
if (!console) {
	var console = {log: function() {}};
}

var dojoConfig = {
    baseUrl: "js/",
    tlmSiblingOfDojo: false,
    packages: [
        { name: "dojo", location: "lib/dojo/" }
    ]
};
oaaLoaderConfig = {
		proxy: "proxy.php"
};
</script>
<script type="text/javascript" data-dojo-config="async: true" src="js/lib/dojo/dojo.js"></script>
<script type="text/javascript" src="js/loader.js"></script>
<script type="text/javascript" src="js/PageManager.js"></script>
<script type="text/javascript">
require(["UT/Hans/AutoMicrosite/Mashup", dojo/ready"], function(Mashup, ready){
	ready(function() {
		var mashup = new Mashup("mashup");
		mashup.loadWidgets({$widgetData});
	});
});
</script>';


$template = new \UT\Hans\AutoMicrosite\Template\MicrodataTemplate(
	'http://localhost/Automated-generation-of-microsites/AutoMicrosite/Templates/Simple.html'
	);

$template->setTitle('My Cool Site');
$template->appendToHead($openAjaxHub);

$templateSlots = $template->getSlots();

$template->setSlot($templateSlots->item(0), '<span>test</span>');

echo $template->getHtml();

?>