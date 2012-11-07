<?php
namespace UT\Hans\AutoMicrosite\Widget;

/**
 * Widget class
 */
class Widget {

	/**
	 * Metadata file location
	 * @var string
	 */
	public $metadataFile;
	
	/**
	 * Placeholder ID where to place the widget
	 * 
	 * @var string 
	 */
	public $placeholder;


	/**
	 * Widget number in hub
	 *
	 * @var int
	 */
	public $orderNumber;

	/**
	 *
	 * @var string
	 */
	public $horizontalPosition;

	/**
	 *
	 * @var string
	 */
	public $verticalPosition;

	/**
	 * Width with units
	 *
	 * @var string
	 */
	public $width;

	/**
	 * Height with units
	 * 
	 * @var string
	 */
	public $height;

	/**
	 * Priority
	 * 
	 * @var int
	 */
	public $priority;
	
	/**
	 * Whether widget contains user interface
	 * 
	 * @var boolean 
	 */
	public $visual;

	/**
	 * Return widget order number in hub
	 *
	 * @return int
	 */
	public function getOrderNumber() {
		return $this->orderNumber;
	}

	/**
	 * Set widget number in hub
	 *
	 * @param int $number
	 */
	public function setOrderNumber($number) {
		$this->orderNumber = $number;
	}

	public function __construct($fileUrl) {
		$this->metadataFile = $fileUrl;
		// TODO: check that file actually exists
	}

	/**
	 * Return widget in JSON format for inclusion in hub
	 * @return string
	 */
	public function toJson() {
		return json_encode($this);
	}

}

?>