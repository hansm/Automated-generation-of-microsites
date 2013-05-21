<?php
namespace UT\Hans\AutoMicrosite;

use RuntimeException;
use ErrorException;

/**
 * Hub creation class
 */
class Hub {

	/**
	 * Mashup title
	 *
	 * @var string
	 */
	private $title;

	/**
	 *
	 * @var \UT\Hans\AutoMicrosite\Template
	 */
	private $template;

	/**
	 * Widgets to the hub
	 *
	 * @var array
	 */
	private $widgets;

	/**
	 * Return mashup title
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Set mashup title
	 *
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Get hub widgets
	 *
	 * @return \UT\Hans\AutoMicrosite\Widget[]
	 */
	public function getWidgets() {
		return $this->widgets;
	}

	/**
	 * Set template value
	 *
	 * @param UT\Hans\AutoMicrosite\Template $template
	 */
	public function setTemplate(Template $template) {
		$this->template = $template;
	}

	public function __construct($title, array $widgets) {
		$this->widgets = array();
		$this->setTitle($title);
		$this->attachWidgets($widgets);
	}

	/**
	 * Attach widget to hub
	 *
	 * @param \UT\Hans\AutoMicrosite\Widget $widget
	 */
	public function attachWidget(Widget $widget) {
		$this->widgets[] = $widget;
	}

	public function attachWidgets(array $widgets) {
		foreach ($widgets as $widget) {
			$this->attachWidget($widget);
		}
	}

	/**
	 * Return hub HTML code
	 *
	 * @return string
	 */
	public function toHtml() {
		try {
			$openAjaxHubHeaders = \file_get_contents(ROOT .'OpenAjaxHubHeaders.html');
		} catch (ErrorException $e) {
			throw new RuntimeException('Could not load OpenAjax hub headers.');
		}
		$this->template->setTitle($this->getTitle());
		$openAjaxHubHeaders = \str_replace('{$widgetData}', $this->widgetsJson(),
												$openAjaxHubHeaders);
		$this->template->appendToHead($openAjaxHubHeaders);

		return $this->template->toHtml();
	}

	/**
	 * Return widget data in JSON format for inclusion in the hub
	 * @return string
	 */
	private function widgetsJson() {
		$json = '[';
		foreach ($this->widgets as $widget) {
			if ($json !== '[') {
				$json .= ',';
			}
			$json .= $widget->toJson();
		}
		$json .= ']';
		return $json;
	}

}
