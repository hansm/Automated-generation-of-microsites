<?php
namespace UT\Hans\AutoMicrosite;

use ErrorException;
use RuntimeException;
use UT\Hans\AutoMicrosite\RuleServiceClient\IWidget as RuleServiceClientWidget;
use UT\Hans\AutoMicrosite\RuleGenerator\IWidget as RuleGeneratorWidget;
use UT\Hans\AutoMicrosite\Request\IRequestWidget;

/**
 * Widget class
 */
class Widget implements RuleGeneratorWidget {

	/**
	 * Unique ID of the widget
	 *
	 * @var string
	 */
	public $id;

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

	public function getId() {
		return $this->id;
	}

	public function getUrl() {
		return $this->metadataFile;
	}

	public function __construct($fileUrl, array $properties = array()) {
		$this->id = self::generateId();
		$this->metadataFile = $fileUrl;
		$this->properties = empty($properties) ? null : $properties;

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
		$this->separatePage = $widget->separatePage();
	}

	/**
	 * Return widget in JSON format for inclusion in hub
	 * @return string
	 */
	public function toJson() {
		return \json_encode($this);
	}

	/**
	 * Generate ID for the widget
	 *
	 * @staticvar int $widget
	 * @return int
	 */
	public static function generateId() {
		static $widget = 0;
		$widget++;
		return $widget;
	}

	/**
	 * Create widget objects from request widgets
	 *
	 * @param \UT\Hans\AutoMicrosite\Request\IRequestWidget[] $requestWidgets
	 * @return array
	 */
	public static function createFromRequestWidgets(array $requestWidgets) {
		$widgets = array();
		foreach ($requestWidgets as $widget) {
			$widgets[] = new Widget($widget->getUrl(), $widget->getProperties());
		}
		return $widgets;
	}

}
