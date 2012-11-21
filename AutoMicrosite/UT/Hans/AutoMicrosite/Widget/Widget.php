<?php
namespace UT\Hans\AutoMicrosite\Widget;

use ErrorException;

/**
 * Widget class
 */
class Widget {
	
	/**
	 * Widget title, used for for example menu
	 * 
	 * @var string 
	 */
	public $title;

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
	public $minWidth;
	public $maxWidth;

	/**
	 * Height with units
	 * 
	 * @var string
	 */
	public $height;
	public $minHeight;
	public $maxHeight;

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
		
		try {
			$metadata = \file_get_contents($this->metadataFile);
			if (\preg_match('#<title>(.+?)</title>#s', $metadata, $titleMatch)) {
				$this->title = trim($titleMatch[1]);
			}
		} catch (ErrorException $e) {
			throw new \RuntimeException('Could not load widget.');
		}
		
		// TODO: this is done using rules
		$this->priority = strpos($this->metadataFile, 'Table') !== false ? 1 : 2;
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