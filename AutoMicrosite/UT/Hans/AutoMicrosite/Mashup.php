<?php
namespace UT\Hans\AutoMicrosite;

use UT\Hans\AutoMicrosite\Widget\Widget;

/**
 * Masup creation happens here
 *
 * @author Hans
 */
class Mashup {

	/**
	 * Hub object
	 * 
	 * @var  \UT\Hans\AutoMicrosite\Hub
	 */
	private $hub;

	/**
	 * Mashup title
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Set mashup title
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
		$this->hub->setTitle($title);
	}

	public function __construct() {
		$this->hub = new Hub();
	}

	/**
	 * Add widget into mashup
	 *
	 * @param string $widgetFile
	 * @throws \UT\Hans\AutoMicrosite\Widget\WidgetException
	 */
	public function addWidget($widgetFile) {
		$widget = new Widget($widgetFile);
		// $widget->loadWidgetData();
		$this->hub->attachWidget($widget);
	}

	public function applyRules() {
		$rulesetId = $this->hub->createRuleset();
		$templateUrl = $this->hub->selectTemplate($rulesetId);
		if (!isset($templateUrl)) {
			throw new \RuntimeException('Template not found.');
			// TODO: someone should probably catch this exception
		}
		
		$this->hub->selectWidgetPositions($rulesetId, $templateUrl);
		
		//$this->hub->applyRules();
	}

	/**
	 * Output mashup HTML code
	 */
	public function output() {
		echo $this->hub->toHtml();
	}

}

?>