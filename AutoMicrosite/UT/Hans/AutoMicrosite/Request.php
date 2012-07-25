<?php
namespace UT\Hans\AutoMicrosite;

/**
 * Handle user requests
 *
 * @author Hans
 */
class Request {

	/**
	 * Directory where widgets are located
	 *
	 * @var string
	 */
	public $widgetsDir;

	public function __construct() {
		$this->widgetsDir = ROOT .'widgets/';

		if (isset($_POST['build'])) {
			$this->build();
		} else {
			$this->form();
		}
	}

	/**
	 * Maship creation form
	 */
	public function form() {
		// find widgets
		$widgetsDir = \dir($this->widgetsDir);
		if (!$widgetsDir) die('Could not load widgets.');
		$widgets = array();
		while ($file = $widgetsDir->read()) {
			if ($file != '.' && $file != '..' && is_dir($this->widgetsDir . $file .'/')) {
				$widgets[] = $file .'/'. $file .'.oam.xml';
			}
		}
		$widgetsDir->close();

		echo '<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Create new mashup</title>
<body>
<form action="" method="post">
  <p>Title: <input type="text" name="title" /></p>';
		foreach ($widgets as $widget) {
			echo '
  <p><label><input type="checkbox" name="widgets[]" value="http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] .'Widgets/'. $widget .'" /> '. $widget .'</label></p>';
		}
		echo '
  <p><input type="submit" name="build" value="Build" /></p>
</form>
</body>
<html>';
	}

	/**
	 * Build mashup
	 */
	public function build() {
		$mashup = new Mashup();
		$mashup->setTitle(htmlentities($_POST['title']));

		if (isset($_POST['widgets']) && is_array($_POST['widgets'])) {
			foreach ($_POST['widgets'] as $widget) {
				$mashup->addWidget($widget);
			}
		}

		$mashup->applyRules();
		$mashup->output();
	}

}

?>
