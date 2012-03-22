<?php
namespace UT\Hans\AutoMicrosite;

/**
 * Masup creation happens here
 *
 * @author Hans
 */
class Mashup {

	/**
	 * Hub object
	 * @var  Hub
	 */
	private $hub;

	/**
	 * Mashup title
	 * @var string
	 */
	private $title;

	private $widgetPriorityCalculator;

	/**
	 * Set mashup title
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	public function __construct() {
		$this->hub = new Hub();
		$this->widgetPriorityCalculator = new WidgetPriority();
	}

	/**
	 * Load widget file into mashup
	 * @param string $widgetFile
	 * @throws WidgetException
	 */
	public function loadWidget($widgetFile) {
		$widget = new Widget($widgetFile);
		$widget->loadWidgetData();
		$widget->setPriority($this->widgetPriorityCalculator->find($widget));
		$this->hub->addWidget($widget);
	}

	/**
	 * Use guidelines, rules extracted from other sites, information gathered about widgets to position them
	 */
	public function positionWidgets() {
		// TODO: some magic

		// TODO: use rules to get general position (top, bottom, left, right, middle)
		/*
		TOP LEFT	TOP		TOP RIGHT
		LEFT		MIDDLE	RIGHT
		BOTTOM LEFT	BOTTOM	BOTTOM RIGHT
		 */
		// TODO: use priority, and possibly even more rules, to determin order in general position
		// TODO: calculate dimensions (percentages? maybe should be done in JavaScript since JavaScript knows window dimensions)
		foreach ($this->hub->getWidgets() as $widget) {
			$widget->setPosition("center-center");
		}
	}

	/**
	 * Output mashup HTML code
	 */
	public function output() {
		echo $this->hub->toHtml();
	}

}

?>