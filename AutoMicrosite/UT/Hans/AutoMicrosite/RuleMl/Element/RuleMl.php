<?php
namespace UT\Hans\AutoMicrosite\RuleMl\Element;

use DOMDocument;
use UT\Hans\AutoMicrosite\RuleMl\RuleMlException;

/**
 * Root RuleML element
 */
class RuleMl extends AbstractContainer {

	protected $elementName = 'RuleML';

	public function appendChild(IRuleMl $element) {
		if (!($element instanceof Assert)) {
			throw new RuleMlException('\''. $this->getElementName() .'\' cannot contain \''. $element->getElementName() .'\'');
		}
		parent::appendChild($element);
	}

	/**
	 *
	 * @return \DOMDocument
	 */
	public function getDomDocument() {
		$document = new DOMDocument('1.0', 'UTF-8');
		$document->appendChild($this->getDom($document));
		return $document;
	}
	
}

?>
