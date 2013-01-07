<?php
namespace UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement;

abstract class AbstractRuleMl implements IRuleMl {

	/**
	 * RuleML element name
	 *
	 * @var string
	 */
	protected $elementName = 'RuleML';

	public function getElementName() {
		return $this->elementName;
	}

}
