<?php
namespace UT\Hans\AutoMicrosite;

/**
 * Hub creation class
 */
class Hub {

	/**
	 * JavaScript files that need to be loaded for the hub
	 * @var array
	 */
	private $jsFiles = array();


	/**
	 * CSS files that need to be loaded for the hub
	 * @var array
	 */
	private $cssFiles = array();

	private $title = 'My Mashup';

	/**
	 * Widgets to the hub
	 * @var array
	 */
	private $widgets = array();

	public function getWidgets() {
		return $this->widgets;
	}

	public function __construct() {
	}

	public function toHtml() {
		return '<!DOCTYPE html>
<html>'. $this->htmlHeader() . $this->htmlBody() .'
<html>';
	}

	/**
	 * Add widget to hub
	 * @param Widget $widget
	 */
	public function addWidget(Widget $widget) {
		$this->widgets[] = $widget;
	}

	private function htmlHeader() {
		return '
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>'. $this->title .'</title>
<style type="text/css">
body {
	font-family: sans-serif;
	font-size: 1em;
	color: #000;
	margin: 0;
	padding: 0;
}
#mapOne {
	margin-top: 50px;
}
.line {
	width: 100%;
	overflow: hidden;
}
.line.top {
}
.line.middle {
}
.line.bottom {
}
.line>.left {
	float: left;
	width: 20%;
}
.line>.center {
	float: left;
}
.line>.right {
	float: right;
	width: 20%;
}
</style>
<script type="text/javascript" src="js/OpenAjaxManagedHub-all.js"></script>
<script type="text/javascript">
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
</head>';
	}

	private function htmlBody() {
		return '
<body>
  <div id="mashup">Loading widgets...</div>
  <script type="text/javascript">
	require(["UT/Hans/AutoMicrosite/Mashup"], function(Mashup){
		var mashup = new Mashup('. $this->widgetsJson() .', "mashup");
		mashup.loadWidgets();
	});
  </script>
</body>';
	}

	/**
	 * Return widget data in JSON format for inclusion in the hub
	 * @return string
	 */
	private function widgetsJson() {
		$json = '[';
		foreach ($this->widgets as $widget) {
			if ($json != '[') {
				$json .= ',';
			}
			$json .= $widget->toJson();
		}
		$json .= ']';
		return $json;
	}

}