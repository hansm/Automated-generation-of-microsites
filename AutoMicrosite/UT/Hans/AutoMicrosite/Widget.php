<?php
namespace UT\Hans\AutoMicrosite;

use ErrorException;
use RuntimeException;
use UT\Hans\AutoMicrosite\RuleServiceClient\IWidget as RuleServiceClientWidget;

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
	 * Widget requires a separate page (when multiple widgets are placed in a single placeholder)
	 *
	 * @var boolean
	 */
	public $separatePage;

	/**
	 * Whether widget contains user interface
	 *
	 * @var boolean
	 */
	public $visual;

	/**
	 * Whether the widget is data widget (non-visual)
	 *
	 * @var boolean
	 */
	public $isDataWidget;

	/**
	 * Mapings generated for semantic integration widget
	 * Published to ee.stacc.transformer.mapping.add.raw topic
	 *
	 * @var string
	 */
	public $mappings;

	/**
	 * Widget properties
	 *
	 * @var array
	 */
	public $properties;

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
			$titleMatch = '';
			if (\preg_match('#<title>(.+?)</title>#s', $metadata, $titleMatch)) {
				$this->title = trim($titleMatch[1]);
			}
		} catch (ErrorException $e) {
			throw new RuntimeException('Could not load widget.');
		}
	}

	/**
	 * Set data received from rule service
	 *
	 * @param \UT\Hans\AutoMicrosite\RuleServiceClient\IWidget $widget
	 */
	public function setData(RuleServiceClientWidget $widget) {
		$this->placeholder = $widget->getPlaceholder();
		$this->priority = $widget->getPriority();
		$this->isDataWidget = $widget->isDataWidget();
	}

	/**
	 * Return widget in JSON format for inclusion in hub
	 * @return string
	 */
	public function toJson() {
		return json_encode($this);
	}

}
