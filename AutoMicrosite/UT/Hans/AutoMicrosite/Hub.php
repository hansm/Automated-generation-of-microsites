<?php
namespace UT\Hans\AutoMicrosite;

use UT\Hans\AutoMicrosite\Widget\Widget;
use UT\Hans\AutoMicrosite\Clients\RuleMlServiceClient;
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
	 * Templates management object
	 * 
	 * @var \UT\Hans\AutoMicrosite\Template\Templates 
	 */
	private $templates;
	
	/**
	 *
	 * @var \UT\Hans\AutoMicrosite\Template\MicrodataTemplate 
	 */
	private $template;
	
	private $rulesetId;

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
	 * Widgets to the hub
	 *
	 * @var array
	 */
	private $widgets = array();

	/**
	 * Get hub widgets
	 *
	 * @return array
	 */
	public function getWidgets() {
		return $this->widgets;
	}

	public function __construct() {
		$this->templates = new Templates();
	}

	/**
	 * Generate widget order number (needed for rule engine)
	 *
	 * @return int
	 */
	public function getNextWidgetOrderNumber() {
		$nextWidgetNumber = $this->widgetsNumber;
		$this->widgetsNumber++;
		return $nextWidgetNumber;
	}

	/**
	 * Attach widget to hub
	 *
	 * @param \UT\Hans\AutoMicrosite\Widget\Widget $widget
	 */
	public function attachWidget(Widget $widget) {
		$widgetNumber = $this->getNextWidgetOrderNumber();
		$widget->setOrderNumber($widgetNumber);
		$this->widgets[$widgetNumber] = $widget;
	}
	
	/**
	 * Create ruleset and send to RuleML service
	 * 
	 * @return int 
	 */
	public function createRuleset() {
		$client = new RuleMlServiceClient(self::RULEML_SERVICE_URL);

		$rulesFile = \file_get_contents(self::RULES_FILE);
		$rulesUtilFile = \file_get_contents(self::RULES_FILE_UTIL);

		// rules
		$rules = RuleMl::createFromString($rulesFile);
		$rules->merge(RuleMl::createFromString($rulesUtilFile));

		// add widget facts
		$transform = new OpenAjaxToRuleMl();
		foreach ($this->widgets as $widget) {
			if (strpos($widget->metadataFile, 'DataManager') !== false) { // TODO: this is bad
				continue;
			}
			$rules->merge($transform->transformString(\file_get_contents($widget->metadataFile), $widget->getOrderNumber()));
		}

		// add templates facts
		$rules->merge($this->templates->getRuleMl());

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
		$client = new RuleMlServiceClient(self::RULEML_SERVICE_URL);
		
		// create query
		$queryString = \file_get_contents('Rules/TemplateQuery.ruleml');
		$query = RuleMlQuery::createFromString($queryString);
		
		// query ruleserver
		$result = $client->query($rulesetId, $query);
		
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
		$client = new RuleMlServiceClient(self::RULEML_SERVICE_URL);
		
		foreach ($this->widgets as $widget) {
			if (strpos($widget->metadataFile, 'DataManager') !== false) { // TODO: this is bad
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
		global $openAjaxHub; // TODO: remove
		
		$this->template->setTitle($this->getTitle());
		
		$openAjaxHub = \str_replace('{$widgetData}', $this->widgetsJson(), $openAjaxHub);
		
		$this->template->appendToHead($openAjaxHub);
		/*
		$content = \file_get_contents(self::TEMPLATE_DIR .'Hub.html');
		$content = \str_replace(
			array('{$title}', '{$widgetData}'),
			array($this->getTitle(), $this->widgetsJson()),
			$content);*/
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