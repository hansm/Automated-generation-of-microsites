<?php
namespace UT\Hans\AutoMicrosite\RuleGenerator;

/**
 * Build rule generator object
 */
class Factory {

	/**
	 * @param string $rulesType
	 * @return \UT\Hans\AutoMicrosite\RuleGenerator\IRuleGenerator
	 * @throws \RuntimeException
	 */
	public static function build($rulesType) {
		switch (\strtoupper($rulesType)) {
			case 'RULEML':
				return new RuleMlGenerator();
		}
		throw new \RuntimeException('Rule generator not implemented.');
	}

}
