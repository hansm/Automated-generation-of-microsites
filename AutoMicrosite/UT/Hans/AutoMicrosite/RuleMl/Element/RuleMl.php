<?php
namespace UT\Hans\AutoMicrosite\RuleMl\Element;

abstract class RuleMl {

	const RULML_NS = 'http://ruleml.org/spec';
	
	/**
	 * RuleML element name
	 * @var string 
	 */
	protected $elementName = 'RuleML';
	
	public function getElementName() {
		return $this->elementName;
	}
	
	abstract function getDom(\DOMDocument $document);
	
}

?>
