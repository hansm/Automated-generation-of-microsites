<?php
namespace UT\Hans\AutoMicrosite;

use UT\Hans\AutoMicrosite\Widget\Widget;
use UT\Hans\AutoMicrosite\RuleServiceClient\Factory as RuleServiceFactory;
use UT\Hans\AutoMicrosite\RuleGenerator\Factory as RuleGeneratorFactory;
use UT\Hans\AutoMicrosite\MappingsGenerator\Factory as MappingsGeneratorFactory;
use ErrorException;
use RuntimeException;

/**
 * Masup creation happens here
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
	 * Application configurations
	 *
	 * @var string[][]
	 */
	private $conf;

	/**
	 * Set mashup title
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
		$this->hub->setTitle($title);
	}

	public function __construct(array $conf) {
		$this->hub = new Hub();
		$this->conf = $conf;
	}

	/**
	 * Get configuration value
	 *
	 * @param string $sectionName
	 * @param string $confName
	 * @return array|string|NULL
	 */
	public function getConf($sectionName = null, $confName = null) {
		if (isset($sectionName) && isset($confName)) {
			return isset($this->conf[$sectionName][$confName]) ? $this->conf[$sectionName][$confName] : null;
		} elseif (isset($sectionName)) {
			return isset($this->conf[$sectionName]) ? $this->conf[$sectionName] : null;
		}
		return $this->conf;
	}

	/**
	 * Add widget into mashup
	 *
	 * @param string $widgetFile
	 * @throws \RuntimeException
	 */
	public function addWidget($widgetFile) {
		$widget = new Widget($widgetFile);
		// $widget->loadWidgetData();
		$this->hub->attachWidget($widget);
	}

	public function addWidgets(array $widgets) {
		foreach ($widgets as $widget) {
			$this->addWidget($widget);
		}
	}

	/**
	 *
	 * @param string $title
	 * @param \UT\Hans\AutoMicrosite\Widget[] $widgets
	 * @return string
	 * @throws \RuntimeException
	 */
	public function process($title, array $widgets) {
		$hub = new Hub();
		$hub->setTitle($title);
		$hub->attachWidgets($widgets);

		try {
			$generalizationRules = \file_get_contents($this->getConf('rules', 'generalization'));
			$priorityRules = \file_get_contents($this->getConf('rules', 'priority'));
			$utilRules = \file_get_contents($this->getConf('rules', 'other'));

			$templateQuery = \file_get_contents($this->getConf('rules', 'template_query'));
			$widgetQuery = \file_get_contents($this->getConf('rules', 'widget_info_query'));
		} catch (ErrorException $e) {
			throw new RuntimeException('Could not read query files.');
		}

		$ruleGenerator = RuleGeneratorFactory::build($this->getConf('general', 'rule_generator'));

		$widgetUrls = array();
		foreach ($widgets as $widget) {
			$widgetUrls[] = $widget->metadataFile;
		}
		$widgetRules = $ruleGenerator->fromWidgets($widgetUrls);

		$templateUrls = Template\MicrodataTemplate::getAllTemplateFiles(
			$this->getConf('general', 'templates_dir')
		);
		$templateRules = $ruleGenerator->fromTemplates($templateUrls);

		$ruleset = $ruleGenerator->combine($generalizationRules, $priorityRules);
		$ruleset = $ruleGenerator->combine($ruleset, $utilRules);
		$ruleset = $ruleGenerator->combine($ruleset, $widgetRules);
		$ruleset = $ruleGenerator->combine($ruleset, $templateRules);

		$ruleService = RuleServiceFactory::build(
				$this->getConf('rule_service', 'url'),
				$this->getConf('rule_service', 'type'),
				$ruleset,
				$templateQuery,
				$widgetQuery);

		// Query for template
		$template = $ruleService->getTemplate();
		$hub->setTemplate($template);

		// Query all widgets' info
		foreach ($widgets as $widget) {
			$widget->setData($ruleService->getWidgetInfo($widget->orderNumber, $template));
		}

		// Generate widgets' message mappings
		$mappingsGenerator = MappingsGeneratorFactory::build($this->getConf('general', 'mappings_generator'));
		foreach ($widgets as $widget) {
			$widget->mappings = $mappingsGenerator->getMappings($widget->metadataFile);
		}

		return $hub->toHtml();
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
