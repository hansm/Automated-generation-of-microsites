<?php
namespace UT\Hans\AutoMicrosite;

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
	 * Application configurations
	 *
	 * @var string[][]
	 */
	private $conf;

	public function __construct(array $conf) {
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
	 *
	 * @param string $title
	 * @param \UT\Hans\AutoMicrosite\Widget[] $requestWidgets
	 * @return string
	 * @throws \RuntimeException
	 */
	public function process($title, array $requestWidgets) {
		$widgets = Widget::createFromRequestWidgets($requestWidgets);
		$hub = new Hub($title, $widgets);

		try {
			$generalizationRules = \file_get_contents($this->getConf('rules', 'generalization'));
			$priorityRules = \file_get_contents($this->getConf('rules', 'priority'));
			$utilRules = \file_get_contents($this->getConf('rules', 'other'));

			$templateQueryFile = $this->getConf('rules', 'template_query');
			$templateQuery = !empty($templateQueryFile) ?
				\file_get_contents($templateQueryFile) : null;

			$widgetQueryFile = $this->getConf('rules', 'widget_info_query');
			$widgetQuery = !empty($widgetQueryFile) ?
				\file_get_contents($widgetQueryFile) : null;
		} catch (ErrorException $e) {
			throw new RuntimeException('Could not read rule files.');
		}

		$ruleGenerator = RuleGeneratorFactory::build($this->getConf('general', 'rule_generator'));

		$widgetRules = $ruleGenerator->fromWidgets($widgets);

		$templates = Template::getAllTemplateFiles(
			$this->getConf('general', 'templates_dir')
		);
		$templateRules = $ruleGenerator->fromTemplates($templates);

		$ruleset = $ruleGenerator->combine($generalizationRules, $priorityRules);
		$ruleset = $ruleGenerator->combine($ruleset, $utilRules);
		$ruleset = $ruleGenerator->combine($ruleset, $widgetRules);
		$ruleset = $ruleGenerator->combine($ruleset, $templateRules);

		$ruleService = RuleServiceFactory::build(
						$this->getConf('rule_service', 'type'),
						$this->getConf('rule_service', 'url'),
						$ruleset,
						$templateQuery,
						$widgetQuery);

		// Query for template
		$templateId = $ruleService->getTemplate();
		foreach ($templates as $t) {
			if ($t->getId() == $templateId) {
				$template = $t;
				break;
			}
		}
		if (!isset($template)) {
			throw new RuntimeException('Invalid template.');
		}

		$hub->setTemplate($template);

		// Query all widgets' info
		foreach ($widgets as $widget) {
			$widget->setData($ruleService->getWidgetInfo($widget->getId(), $templateId));
		}

		// Generate widgets' message mappings
		$mappingsGenerator = MappingsGeneratorFactory::build($this->getConf('general', 'mappings_generator'));
		foreach ($widgets as $widget) {
			$widget->mappings = $mappingsGenerator->getMappings($widget->metadataFile);
		}

		return $hub->toHtml();
	}

}
