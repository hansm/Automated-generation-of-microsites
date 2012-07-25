<?php
namespace UT\Hans\AutoMicrosite\Widget;

/**
 * Description of WidgetPriority
 *
 * @author Hans
 */
class WidgetPriority {

	public function find(Widget $widget) {
		// TODO: actually do some smart calculations here
		// using statistics seems like a good idea
		// TODO: maybe I should add priorities of categories instead of max???
		$categories = $widget->getCategories();
		$maxPriority = 0;
		foreach ($categories as $category) {
			$priority = $this->categoryPriority($category);
			if ($priority > $maxPriority) {
				$maxPriority = $priority;
			}
		}
		return $maxPriority;
	}

	private function categoryPriority($category) {
		// TODO: do something smart here too
		switch ($category) {
			case 'Content':
				return 10;
			case 'Visualization':
				return 5;
			case 'Navigation':
				return 1;
			default:
				return 0;
		}
	}

}

?>