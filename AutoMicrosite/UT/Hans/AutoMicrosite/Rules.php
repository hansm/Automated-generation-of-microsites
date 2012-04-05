<?php
namespace UT\Hans\AutoMicrosite;

/**
 * Rules engine
 *
 * @author Hans
 */
class Rules {
	
	/**
	 * Widgets for to apply rules for
	 * @var array 
	 */
	private $widgets = array();
	
	public function __construct() {
	}
	
	/**
	 * Add widget to rules engine
	 * @param Widget $widget 
	 */
	public function addWidget(Widget $widget) {
		$this->widgets[] = $widget;
	}
	
	public function apply() {
		foreach ($this->widgets as $widget) {
			$widget->setPosition('center-center');
		}
	}
	
}

?>