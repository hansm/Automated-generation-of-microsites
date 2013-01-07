<?php
namespace UT\Hans\AutoMicrosite\RuleGenerator;

use RuntimeException;

/**
 * RuleML relations
 */
class Relation {

	/**
	 * Widget exists
	 */
	const WIDGET = 'http://automicrosite.maesalu.com/#widgetExists';

	/**
	 * Widget belongs to category
	 */
	const WIDGET_CATEGORY = 'http://openajax.org/metadata#category';

	/**
	 * Widget is data widget (non-visual)
	 */
	const WIDGET_IS_DATA = 'http://automicrosite.maesalu.com/#isDataWidget';

	/**
	 * Widget priority value (priority2 because it has default value)
	 */
	const WIDGET_PRIORITY = 'http://automicrosite.maesalu.com/#priority2';

	/**
	 * Template exists
	 */
	const TEMPLATE = 'http://automicrosite.maesalu.com/#templateExists';

	/**
	 * Template fits widgets relation
	 * Widgets for all template placeholders
	 */
	const TEMPLATE_WIDGETS = 'http://automicrosite.maesalu.com/#template';

	/**
	 * All visual widgets have a placeholders in a template
	 */
	const WIDGETS_TEMPLATE = '';

	/**
	 * Widget matches placeholder in a template
	 */
	const WIDGET_PLACE = 'http://automicrosite.maesalu.com/#widgetPlace';

	const TEMPLATE_MIN_HEIGHT = 'http://automicrosite.maesalu.com/TemplatePlaceholder#min-height';

	const TEMPLATE_MAX_HEIGHT = 'http://automicrosite.maesalu.com/TemplatePlaceholder#max-height';

	const TEMPLATE_MIN_WIDTH = 'http://automicrosite.maesalu.com/TemplatePlaceholder#min-width';

	const TEMPLATE_MAX_WIDTH = 'http://automicrosite.maesalu.com/TemplatePlaceholder#max-width';

	/**
	 * Whether template allows multiple widgets
	 */
	const TEMPLATE_MULTIPLE = 'http://automicrosite.maesalu.com/TemplatePlaceholder#multiple';

	/**
	 * Template allows category in a placeholder
	 */
	const PLACE_CATEGORY = 'http://automicrosite.maesalu.com/TemplatePlaceholder#category';

	/**
	 * Get template dimensions relation based on variable name
	 *
	 * @param string $dimensionVar
	 * @return string
	 * @throws \RuntimeException
	 */
	public static function getTemplateDimensionsRel($dimensionVar) {
		switch ($dimensionVar) {
			case 'min-height':
				return self::TEMPLATE_MIN_HEIGHT;
			case 'max-height':
				return self::TEMPLATE_MAX_HEIGHT;
			case 'min-width':
				return self::TEMPLATE_MIN_WIDTH;
			case 'max-width':
				return self::TEMPLATE_MAX_WIDTH;
		}
		throw new RuntimeException('Relation does not exist.');
	}

}
