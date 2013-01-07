<?php
namespace UT\Hans\AutoMicrosite\RuleGenerator;

/**
 * Rule generator interface
 */
interface IRuleGenerator {

	/**
	 * Create RuleML from template files
	 *
	 * @param \UT\Hans\AutoMicrosite\RuleGenerator\ITemplate[] $templates
	 * @return string
	 * @throws \RuntimeException
	 */
	public function fromTemplates(array $templates);

	/**
	 * Create RuleML from widget files
	 *
	 * @param \UT\Hans\AutoMicrosite\RuleGenerator\IWidget[] $widgets
	 * @return string
	 * @throws \RuntimeException
	 */
	public function fromWidgets(array $widgets);

	/**
	 * Combine 2 rulesets into 1
	 *
	 * @param string $ruleset1
	 * @param string $ruleset2
	 * @return string
	 * @throws \RuntimeException
	 */
	public function combine($ruleset1, $ruleset2);

}
