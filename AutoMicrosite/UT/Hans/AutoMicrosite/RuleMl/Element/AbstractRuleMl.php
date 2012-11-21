<?php
namespace UT\Hans\AutoMicrosite\RuleMl\Element;

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

?>
