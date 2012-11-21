<?php
namespace UT\Hans\AutoMicrosite\RuleMl\Element;

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
	 * @throws \UT\Hans\AutoMicrosite\RuleMl\RuleMlException
	 */
	public function getDom(\DOMDocument $document);

}

?>