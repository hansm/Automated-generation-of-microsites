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

	/**
	 * Flow order number, whenever available
	 *
	 * @var int|null
	 */
	private $flowOrder;

	public function getProperties() {
		return $this->properties;
	}

	public function getUrl() {
		return $this->url;
	}

	public function getFlowOrder() {
		return $this->flowOrder;
	}

	/**
	 * @param string $url
	 * @param array $properties
	 * @param int|null $flowOrder
	 */
	public function __construct($url, $properties = array(), $flowOrder = null) {
		$this->url = $url;
		$this->properties = $properties;
		$this->flowOrder = $flowOrder;
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
