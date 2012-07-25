<?php
namespace UT\Hans\AutoMicrosite;

use \UT\Hans\AutoMicrosite\Widget\Widget;

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
		$this->hub->applyRules();
	}

	/**
	 * Output mashup HTML code
	 */
	public function output() {
		echo $this->hub->toHtml();
	}

}

?>