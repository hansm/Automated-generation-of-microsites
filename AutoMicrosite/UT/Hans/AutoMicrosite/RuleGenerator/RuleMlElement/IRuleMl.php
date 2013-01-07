<?php
namespace UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement;

interface IRuleMl {

	const RULML_NS = 'http://ruleml.org/spec';

	/**
	 * Get the name of the element in XML
	 *
	 * @return string
	 */
	public function getElementName();

	/**
	 * Get PHP dome element
	 *
	 * @param DOMDocument $document
	 * @return \DOMElement
	 * @throws \RuntimeException
	 */
	public function getDom(\DOMDocument $document);

}
