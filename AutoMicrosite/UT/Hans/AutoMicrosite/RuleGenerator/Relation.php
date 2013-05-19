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
	const WIDGET = 'http://deepweb.ut.ee/#widgetExists';

	/**
	 * Widget belongs to category
	 */
	const WIDGET_CATEGORY = 'http://openajax.org/metadata#category';

	/**
	 * Widget is data widget (non-visual)
	 */
	const WIDGET_IS_DATA = 'http://deepweb.ut.ee/#isDataWidget';

	/**
	 * Widget is menu widget
	 */
	const WIDGET_IS_MENU = 'http://deepweb.ut.ee/#isMenuWidget';

	/**
	 * Widget priority value (priority2 because it has default value)
	 */
	const WIDGET_PRIORITY = 'http://deepweb.ut.ee/#priority2';

	/**
	 * Widget has to be loaded first
	 */
	const WIDGET_LOAD_FIRST = 'http://deepweb.ut.ee/automicrosite/#loadFirst';

	/**
	 * Widget dimensions
	 */
	const WIDGET_MIN_HEIGHT = 'http://deepweb.ut.ee/TemplatePlaceholder#min-height';
	const WIDGET_MAX_HEIGHT = 'http://deepweb.ut.ee/TemplatePlaceholder#max-height';
	const WIDGET_MIN_WIDTH = 'http://deepweb.ut.ee/TemplatePlaceholder#min-width';
	const WIDGET_MAX_WIDTH = 'http://deepweb.ut.ee/TemplatePlaceholder#max-width';

	/**
	 * Template exists
	 */
	const TEMPLATE = 'http://deepweb.ut.ee/#templateExists';

	/**
	 * Template placeholder
	 */
	const PLACEHOLDER = 'http://deepweb.ut.ee/automicrosite/#placeholder';

	/**
	 * Template fits widgets relation
	 * Widgets for all template placeholders
	 */
	const TEMPLATE_WIDGETS = 'http://deepweb.ut.ee/#template';

	/**
	 * All visual widgets have a placeholders in a template
	 */
	const WIDGETS_TEMPLATE = '';

	/**
	 * Widget matches placeholder in a template
	 */
	const WIDGET_PLACE = 'http://deepweb.ut.ee/#widgetPlace';

	const TEMPLATE_MIN_HEIGHT = 'http://deepweb.ut.ee/TemplatePlaceholder#min-height';

	const TEMPLATE_MAX_HEIGHT = 'http://deepweb.ut.ee/TemplatePlaceholder#max-height';

	const TEMPLATE_MIN_WIDTH = 'http://deepweb.ut.ee/TemplatePlaceholder#min-width';

	const TEMPLATE_MAX_WIDTH = 'http://deepweb.ut.ee/TemplatePlaceholder#max-width';

	/**
	 * Whether template allows multiple widgets
	 */
	const TEMPLATE_MULTIPLE = 'http://deepweb.ut.ee/TemplatePlaceholder#multiple';

	/**
	 * Template allows category in a placeholder
	 */
	const PLACE_CATEGORY = 'http://deepweb.ut.ee/TemplatePlaceholder#category';

	/**
	 * Widget does not fit in placeholder (or vice versa)
	 */
	const BAD_DIMENSIONS = 'http://deepweb.ut.ee/automicrosite/#badDimensions';

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
