<?php
namespace UT\Hans\AutoMicrosite\Request;

use \Exception;

/**
 * Simple GET request handler
 *
 * INPUT:
 *	widget[WIDGET_ID]					URLs of widget metadata files
 *  property[WIDGET_ID][PROPERTY_NAME]	properties
 *	title								title of the mashup
 *
 * Sample:
 * http://localhost/Automated-generation-of-microsites/AutoMicrosite/?widget%5B%5D=http%3A%2F%2Flocalhost%2FAutomated-generation-of-microsites%2FAutoMicrosite%2FWidgets%2FData%2FData.oam.xml&widget%5B%5D=http%3A%2F%2Flocalhost%2FAutomated-generation-of-microsites%2FAutoMicrosite%2FWidgets%2FDataManager%2FDataManager.oam.xml&widget%5B%5D=http%3A%2F%2Flocalhost%2FAutomated-generation-of-microsites%2FAutoMicrosite%2FWidgets%2FMap%2FMap.oam.xml&widget%5B%5D=http%3A%2F%2Flocalhost%2FAutomated-generation-of-microsites%2FAutoMicrosite%2FWidgets%2FMenu%2FMenu.oam.xml&widget%5B%5D=http%3A%2F%2Flocalhost%2FAutomated-generation-of-microsites%2FAutoMicrosite%2FWidgets%2FSummary%2FSummary.oam.xml&widget%5B%5D=http%3A%2F%2Flocalhost%2FAutomated-generation-of-microsites%2FAutoMicrosite%2FWidgets%2FTable%2FTable.oam.xml&title=My+Mashup
 */
class GET extends AbstractRequest {

	public function setInput() {
		if (empty($_REQUEST['widget']) || !\is_array($_REQUEST['widget'])) {
			throw new Exception('No widgets given as input.');
		}

		$widgets = array();
		foreach ($_REQUEST['widget'] as $widgetNumber => $widgetUrl) {
			$widget = new RequestWidget($widgetUrl);
			if (isset($_REQUEST['property'])
					&& !empty($_REQUEST['property'][$widgetNumber])
					&& \is_array($_REQUEST['property'][$widgetNumber])) {
				foreach ($_REQUEST['property'][$widgetNumber] as $propName => $propValue) {
					$widget->addProperty($propName, $propValue);
				}
			}
			$widgets[] = $widget;
		}

		$this->setWidgets($widgets);
		$this->setTitle(isset($_REQUEST['title']) ? $_REQUEST['title'] : 'My mashup');
	}

	public function handleException(Exception $e) {
		echo '<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Error</title>
	</head>
	<body>
		<p>', $e->getMessage() ,'</p>
	</body>
</html>';
	}

	public function response($result) {
		echo $result;
		/*
			$url = $this->saveToFile($result);
			header('Location: '. $url);
		*/
	}

}
