<?php
namespace UT\Hans\AutoMicrosite\Request;

use \Exception;

/**
 * Simple GET request handler
 *
 * INPUT:
 *	widgets[]	URLs of widget metadata files
 *	title		title of the mashup
 *
 * @author Hans
 */
class GET extends AbstractRequest {

	protected function setInput() {
		if (empty($_REQUEST['widgets']) || !\is_array($_REQUEST['widgets'])) {
			throw new Exception('No widgets given as input.');
		}
		$this->setWidgets($_REQUEST['widgets']);
		$this->setTitle(isset($_REQUEST['title']) ? $_REQUEST['title'] : 'My mashup');
	}

	protected function handleException(Exception $e) {
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

	protected function response($result) {
		//
	}

}

?>