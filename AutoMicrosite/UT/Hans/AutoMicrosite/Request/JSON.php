<?php
namespace UT\Hans\AutoMicrosite\Request;

use RuntimeException;
use Exception;

/**
 * JSON request handler
 * {
 *		"title": "My Mashup",
 *		"widgets": [
 *			{
 *				"url": "http://deepweb.ut.ee/automicrosite/Widgets/Table/Table.oam.xml",
 *				"properties": {
 *					"backgroundColor": "#FFFFFF",
 *					"foregroundColor": "#000000"
 *				},
 *				"flowOrder": 1
 *			}
 *		]
 * }
 *
 * @author Hans
 */
class JSON extends AbstractRequest {
	
	public function setInput() {
		$inputData = file_get_contents('php://input');
		if (!$inputData) {
			$inputData = urldecode($_SERVER['QUERY_STRING']);
		}

		$inputObject = \json_decode($inputData);
		if (!$inputObject || empty($inputObject->title)
				|| empty($inputObject->widgets) || !\is_array($inputObject->widgets)) {
			throw new RuntimeException('Invalid input data.');
		}

		$this->setTitle($inputObject->title);

		$widgets = array();
		foreach ($inputObject->widgets as $widget) {
			if (empty($widget->url)) {
				throw new RuntimeException('Invalid widget given.');
			}

			$widgets[] = new RequestWidget($widget->url,
						self::getPropertiesArray($widget),
						isset($widget->flowOrder) ? $widget->flowOrder : null
					);
		}
		$this->setWidgets($widgets);
	}
	
	public function response($result) {
		echo $result;
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

	/**
	 * Convert stdClass to an array
	 *
	 * @param stdClass $widget
	 * @return array
	 */
	private static function getPropertiesArray($widget) {
		$properties = array();

		if (!empty($widget->properties) && \is_object($widget->properties)) {
			foreach ($widget->properties as $key => $val) {
				$properties[$key] = $val;
			}
		}

		return $properties;
	}
}
