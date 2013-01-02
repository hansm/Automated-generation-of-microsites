<?php
namespace UT\Hans\AutoMicrosite\RuleGenerator;

/**
 * Rule generator interface
 */
interface IRuleGenerator {

	/**
	 * Create RuleML from template files
	 *
	 * @param string[] $templateUrls
	 * @return string
	 * @throws \RuntimeException
	 */
	public function fromTemplates(array $templateUrls);

	/**
	 * Create RuleML from widget files
	 *
	 * @param string[] $widgetUrls
	 * @return string
	 * @throws \RuntimeException
	 */
	public function fromWidgets(array $widgetUrls);

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
