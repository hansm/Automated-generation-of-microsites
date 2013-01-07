<?php
namespace UT\Hans\AutoMicrosite;

use RuntimeException;
use ErrorException;
use UT\Hans\AutoMicrosite\Clients\RuleMlServiceClient; // TODO: remove
use UT\Hans\AutoMicrosite\RuleMl\RuleMl;
use UT\Hans\AutoMicrosite\RuleMl\OpenAjaxToRuleMl;
use UT\Hans\AutoMicrosite\RuleMl\RuleMlQuery;
use UT\Hans\AutoMicrosite\Template\Templates;
use UT\Hans\AutoMicrosite\Template\MicrodataTemplate;

/**
 * Hub creation class
 */
class Hub {

	const TEMPLATE_DIR = 'Template/';

	//const RULEML_SERVICE_URL = 'http://localhost:8080/RuleMLApp/RuleMLService';
	const RULEML_SERVICE_URL = 'http://automicrosite.maesalu.com:8080/RuleMlApp2/RuleMLService';

	const RULES_FILE = 'Rules/Rules.ruleml';

	const RULES_FILE_UTIL = 'Rules/Util.ruleml';

	const WIDGET_SELECT_RULES = 'Rules/WidgetSelectRules.ruleml';

	const PRIORITY_RULES = 'Rules/Priority.ruleml';

	const GENERALIZATION_RULES = 'Rules/Generalization.ruleml';

	const TEMPLATE_QUERY = 'Rules/TemplateQuery.ruleml';

	const WIDGET_PLACE_QUERY = 'Rules/WidgetPlaceQuery.ruleml';

	/**
	 * Mashup title
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Number of widgets added to the hub
	 *
	 * @var int
	 */
	private $widgetsNumber = 0;

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
	 * Generate widget order number (needed for rule engine)
	 *
	 * @return int
	 */
	public function getNextWidgetOrderNumber() {
		// TODO: remove this stuff
		$nextWidgetNumber = $this->widgetsNumber;
		$this->widgetsNumber++;
		return $nextWidgetNumber;
	}

	/**
	 * Attach widget to hub
	 *
	 * @param \UT\Hans\AutoMicrosite\Widget $widget
	 */
	public function attachWidget(Widget $widget) {
		$widgetNumber = $this->getNextWidgetOrderNumber();
		$widget->setOrderNumber($widgetNumber);
		$this->widgets[$widgetNumber] = $widget;
	}

	public function attachWidgets(array $widgets) {
		foreach ($widgets as $widget) {
			$this->attachWidget($widget);
		}
	}

	/**
	 * Create ruleset and send to RuleML service
	 *
	 * @return int
	 */
	public function createRuleset() {
		// TODO: remove this
		$client = new RuleMlServiceClient(self::RULEML_SERVICE_URL);

		$rulesUtilFile = \file_get_contents(self::RULES_FILE_UTIL);
		$priorityRulesFile = \file_get_contents(self::PRIORITY_RULES);
		$generalizationRulesFile = \file_get_contents(self::GENERALIZATION_RULES);

		// rules
		$rules = RuleMl::createFromString($priorityRulesFile);
		$rules->merge(RuleMl::createFromString($rulesUtilFile));
		$rules->merge(RuleMl::createFromString($generalizationRulesFile));

		// add widget facts
		$transform = new OpenAjaxToRuleMl();
		foreach ($this->widgets as $widget) {
			$rules->merge($transform->transformString(\file_get_contents($widget->metadataFile), $widget->getOrderNumber()));
		}

		// add templates facts
		//$rules->merge($this->templates->getRuleMl());

print_r($rules->getString());exit();
		$this->rulesetId = $client->create($rules);

		return $this->rulesetId;
	}

	/**
	 * Select template for mashup
	 *
	 * @param int $rulesetId
	 * @return type
	 * @throws \Exception
	 */
	public function selectTemplate($rulesetId) {
		// TODO: remove this
		$client = new RuleMlServiceClient(self::RULEML_SERVICE_URL);

		// create query
		$queryString = \file_get_contents('Rules/TemplateQuery.ruleml');
		$query = RuleMlQuery::createFromString($queryString);

		// query ruleserver
		$result = $client->query($rulesetId, $query);

print_r($queryString);
print_r($result->getString());

		// get result value
		$templateUrl = null;
		$variables = $result->getDom()->getElementsByTagName('Var');
		for ($i = 0; $i < $variables->length; $i++) {
			$value = \explode(':', $variables->item($i)->nextSibling->textContent);
			$value = \trim(\reset($value));

			switch (\trim($variables->item($i)->textContent)) {
				case 'template':
					$templateUrl = trim($value, '"'); // RuleML service adds " to the end and beginning
					break;
			}
		}

		$this->template = $this->templates->getTemplate($templateUrl);

		return $templateUrl;
	}

	public function selectWidgetPositions($rulesetId, $templateUrl) {
		// TODO: remove this
		$client = new RuleMlServiceClient(self::RULEML_SERVICE_URL);

		foreach ($this->widgets as $widget) {
			if (strpos($widget->metadataFile, 'Data') !== false) { // TODO: this is bad
				continue;
			}

			// create query
			$queryString = \file_get_contents(self::WIDGET_PLACE_QUERY);
			$queryString = \str_replace(
				array('{$widget}', '{$template}'),
				array($widget->getOrderNumber(), $templateUrl),
				$queryString); // TODO: this should probably be done using DOM

			$query = RuleMlQuery::createFromString($queryString);
			$result = $client->query($rulesetId, $query);

			$variables = $result->getDom()->getElementsByTagName('Var');
			for ($i = 0; $i < $variables->length; $i++) {
				$value = \explode(':', $variables->item($i)->nextSibling->textContent);
				$value = \trim(\reset($value));

				switch (\trim($variables->item($i)->textContent)) {
					case 'placeholder':
						$widget->placeholder = trim($value, '"');
						break;
					case 'priority':
						$widget->priority = (int) $value;
						break;
					case 'isDataWidget':
						$widget->isDataWidget = strcasecmp(trim($value, '"'), 'true') == 0;
						break;
				}
			}
		}
	}

	/**
	 * Apply rules to get widget information
	 *
	 * @throws \Exception
	 */
	/*
	public function applyRules() {
		$client = new RuleMlServiceClient(self::RULEML_SERVICE_URL);

		$rulesFile = \file_get_contents(self::RULES_FILE);
		$rulesUtilFile = \file_get_contents(self::RULES_FILE_UTIL);

		$rules = RuleMl::createFromString($rulesFile);
		$rules->merge(RuleMl::createFromString($rulesUtilFile));

		// add widget facts
		$transform = new OpenAjaxToRuleMl();
		foreach ($this->widgets as $widget) {
			$rules->merge($transform->transformString(\file_get_contents($widget->metadataFile), $widget->getOrderNumber()));
		}

		$rulesetId = $client->create($rules);

		// query rules engine
		foreach ($this->widgets as $widget) {
			$queryRuleMl = RuleMlQuery::createQuery($widget->getOrderNumber());
			$queryResult = $client->query($rulesetId, $queryRuleMl);

			$variables = $queryResult->getDom()->getElementsByTagName('Var');
			for ($i = 0; $i < $variables->length; $i++) {
				$value = \trim(\reset(\explode(':', $variables->item($i)->nextSibling->textContent)));
				switch (\trim($variables->item($i)->textContent)) {
					case 'locationVertical':
						$widget->verticalPosition = $value;
						break;
					case 'locationHorizontal':
						$widget->horizontalPosition = $value;
						break;
					case 'height':
						$widget->height = (int) $value;
						break;
						break;
					case 'width':
						$widget->width = (int) $value;
						break;
					case 'priority':
						$widget->priority = (int) $value;
						break;
				}
			}

			// TODO: this part has to be automated
			if (\stripos($widget->metadataFile, 'map') !== false) {
				$widget->properties = array(
					'buttons' => array(1997, 1998, 1999, 2000, 2001, 2002, 2003, 2004, 2005, 2006, 2007, 2008)
				);
			} else if (\stripos($widget->metadataFile, 'menu') !== false) {
				$widget->properties = array(
					'buttons' => array(array('label' => 'Map', 'href' => 'map'),
						array('label' => 'Google', 'href' => 'http://www.google.com'))
				);
			}
		}
	}*/

	/**
	 * Return hub HTML code
	 *
	 * @return string
	 */
	public function toHtml() {
		try {
			$openAjaxHubHeaders = \file_get_contents('Template/OpenAjaxHubHeaders.html');
		} catch (ErrorException $e) {
			throw new RuntimeException('Could not load OpenAjax hub headers.');
		}
		$this->template->setTitle($this->getTitle());
		$openAjaxHubHeaders = \str_replace('{$widgetData}', $this->widgetsJson(), $openAjaxHubHeaders);
		$openAjaxHubHeaders = \str_replace('{$templateData}', $this->template->getJson(), $openAjaxHubHeaders);
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
