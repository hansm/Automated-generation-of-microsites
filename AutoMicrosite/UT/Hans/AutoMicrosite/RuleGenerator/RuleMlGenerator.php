<?php
namespace UT\Hans\AutoMicrosite\RuleGenerator;

use DOMDocument;
use RuntimeException;

/**
 * Description of RuleMlGenerator
 */
class RuleMlGenerator implements IRuleGenerator {

	/**
	 * RuleML XML namespace
	 */
	const RULEML_NS = 'http://ruleml.org/spec';

	/**
	 * @var \UT\Hans\AutoMicrosite\RuleGenerator\OpenAjaxToRuleMl
	 */
	private $openAjaxToRuleMl;

	/**
	 * @var \UT\Hans\AutoMicrosite\RuleGenerator\MicrodataTemplateToRuleMl
	 */
	private $microdataTemplateToRuleMl;

	public function __construct() {
		$this->openAjaxToRuleMl = new OpenAjaxToRuleMl();
		$this->microdataTemplateToRuleMl = new MicrodataTemplateToRuleMl();
	}

	public function fromTemplates(array $templates) {
		$ruleset = null;
		foreach ($templates as $template) {
			$templateRuleset = $this->microdataTemplateToRuleMl->transformFile(
								$template->getUrl(),
								$template->getId());
			if (isset($ruleset)) {
				$ruleset = $this->combine($ruleset, $templateRuleset);
			} else {
				$ruleset = $templateRuleset;
			}
		}

		return $ruleset;
	}

	public function fromWidgets(array $widgets) {
		$ruleset = null;
		foreach ($widgets as $widget) {
			$widgetRuleset = $this->openAjaxToRuleMl->transformFile(
								$widget->getUrl(),
								$widget->getId());
			if (isset($ruleset)) {
				$ruleset = $this->combine($ruleset, $widgetRuleset);
			} else {
				$ruleset = $widgetRuleset;
			}
		}

		return $ruleset;
	}

	public function combine($ruleset1, $ruleset2) {
		$document1 = new DOMDocument();
		$document2 = new DOMDocument();
		if (!$document1->loadXML($ruleset1)
				|| !$document2->loadXML($ruleset2)) {
			throw new RuntimeException('Could not load XML.');
		}

		// Second one is empty, return just first
		$parent2 = $document2->hasChildNodes() ?
				$document2->getElementsByTagNameNS(self::RULEML_NS, 'Assert')->item(0) :
				null;
		if (!$parent2 || !$parent2->hasChildNodes()) {
			return $ruleset1;
		}

		// First one is empty, return just second
		$parent1 = $document1->hasChildNodes() ?
				$document1->getElementsByTagNameNS(self::RULEML_NS, 'Assert')->item(0) :
				null;
		if (!$parent1 || !$parent1->hasChildNodes()) {
			return $ruleset2;
		}

		// Append second document rules to the first one
		$children = $parent2->childNodes;
		for ($i = 0; $i < $children->length; $i++) {
			$parent1->appendChild(
				$document1->importNode($children->item($i), true)
			);
		}

		return $document1->saveXML();
	}

}
