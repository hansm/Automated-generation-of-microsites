<?php
namespace UT\Hans\AutoMicrosite\Request;

/**
 * Request widget class
 */
class RequestWidget implements IRequestWidget {

	/**
	 * Widget metadata file URL
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Properties for the widget
	 *
	 * @var array
	 */
	private $properties;

	public function getProperties() {
		return $this->properties;
	}

	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param string $url
	 * @param array $properties
	 */
	public function __construct($url, $properties = array()) {
		$this->url = $url;
		$this->properties = $properties;
	}

	/**
	 * Add property to widget
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function addProperty($name, $value) {
		$this->properties[$name] = $value;
	}

}
