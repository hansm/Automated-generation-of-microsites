<?php
namespace UT\Hans\AutoMicrosite\RuleMl\Element;

use DOMDocument;

/**
 * Abstract container element that can contain several child RuleML elements
 */
abstract class AbstractContainer extends AbstractRuleMl {

	/**
	 * Child RuleML elements
	 *
	 * @var array
	 */
	protected $children = array();

	/**
	 * Get child elements
	 *
	 * @return array
	 */
	public function getChildren() {
		return $this->children;
	}

	public function __construct(array $children = array()) {
		$this->children = $children;
	}

	/**
	 * Append child element
	 * 
	 * @param \UT\Hans\AutoMicrosite\RuleMl\Element\RuleMl $element
	 */
	public function appendChild(IRuleMl $element) {
		$this->children[] = $element;
	}
	
	/**
	 * Append an array of child elements
	 * 
	 * @param array $elements
	 */
	public function appendChildren(array $elements) {
		foreach ($elements as $element) {
			$this->appendChild($element);
		}
	}

	/**
	 *
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public function getDom(DOMDocument $document) {
		$element = $document->createElementNS(RuleMl::RULML_NS, $this->getElementName());

		foreach ($this->getChildren() as $child) {
			$element->appendChild($child->getDom($document));
		}

		return $element;
	}

}

?>