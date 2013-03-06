<?php
namespace UT\Hans\AutoMicrositePortal;

use Lib\Wookie\WookieConnectorService;
use Lib\Wookie\Property;

class Request {
	
	public function __construct() {
		//$connection = new CurlConnection('http://c47-78.uvn.zone.eu:8080/wookie/widgets');
		//print_r($connection->get());
		if (isset($_POST['submit'])) {
			$this->submit();
		}
		$this->htmlHeader();
		$this->form();
		$this->htmlFooter();
	}
	
	private function connect() {
		$client = new WookieConnectorService('http://c47-78.uvn.zone.eu:8080/wookie/', 'AUTOMICROSITE', 'dev2', 'AutoMicrositePortal');
		$client->setLogPath(ROOT .'log' . DIRECTORY_SEPARATOR);
		
		//$client->getUser()->setLoginName('AutoMicrositePortal');
		
		if (!$client->getConnection()->Test()) {
			throw new \RuntimeException('Could not connect to Wookie service.');
		}
		
		return $client;
	}
	
	private function form() {
		$client = $this->connect();
		
		$widgets = $client->getAvailableWidgets();
		foreach ($widgets as $widget) {
			$this->htmlWidget($widget);
		}
		
		
		/*
		
		$newProperty = new Property('demo_property', 'demo_value');
		$result = $client->setProperty($widgetInstance, $newProperty);
		print_r($result);
		
		print_r($client->WidgetInstances->get());*/
		
	}
	
	private function submit() {
		$url = array(
			'title'	=>	$_POST['title']
		);
		
		$client = $this->connect();
		$widgets = $client->getAvailableWidgets();
		foreach ($widgets as $widget) {
			if (\in_array($widget->getIdentifier(), $_POST['widget'])) {
				$widgetInstance = $client->getOrCreateInstance($widget->getIdentifier());
				$url['widget'][] = $widgetInstance->getUrl();
			}
		}
		
		header('Location: http://automicrosite.maesalu.com/demo/?'. http_build_query($url));
		exit();
	}
	
	public function htmlWidget(\Lib\Wookie\Widget $widget) {
		echo '<p>
  <label>
    <input type="checkbox" name="widget[]" value="', \htmlentities($widget->getIdentifier()) ,'" /> ', $widget->getTitle() ,'
  </label><br />
  ', $widget->getDescription() ,'
</p>';
	}
	
	public function htmlHeader() {
		echo '<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Select widgets</title>
	<style type="text/css">
		body {
			font-family: "Segoe UI", sans-serif;
			color: #000;
			margin: 0;
			padding: 1em;
			width: 100%;
			height: 100%;
		}
	</style>
</head>
<body>
<form action="" method="post">
  <p>Title: <input type="text" name="title" /></p>';
	}
	
	public function htmlFooter() {
		echo '  <input type="submit" name="submit" value="Submit" />
</form>
</body>
</html>';
	}
	
}
