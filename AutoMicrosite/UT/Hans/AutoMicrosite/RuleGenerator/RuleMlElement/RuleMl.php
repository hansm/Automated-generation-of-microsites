<?php
namespace UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement;

use DOMDocument;
use RuntimeException;

/**
 * Root RuleML element
 */
class RuleMl extends AbstractContainer {

	const RULML_NS = 'http://ruleml.org/spec';

	protected $elementName = 'RuleML';

	public function appendChild(IRuleMl $element) {
		if (!($element instanceof Assert)) {
			throw new RuntimeException('\''. $this->getElementName() .'\' cannot contain \''. $element->getElementName() .'\'');
		}
		parent::appendChild($element);
	}

	/**
	 * Get DOM object of the RuleML element
	 *
	 * @return \DOMDocument
	 */
	public function getDomDocument() {
		$document = new DOMDocument('1.0', 'UTF-8');
		$document->appendChild($this->getDom($document));
		return $document;
	}

}
