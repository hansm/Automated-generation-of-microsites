<?php
namespace UT\Hans\AutoMicrosite\RuleMl\Element;

use UT\Hans\AutoMicrosite\RuleMl\RuleMlException;

class Implies extends AbstractRuleMl {

	protected $elementName = 'Implies';

	/**
	 * Condition of the implication
	 *
	 * @var IfElement
	 */
	private $if;

	/**
	 * Result of the implication
	 *
	 * @var ThenElement
	 */
	private $then;

	public function getIf() {
		return $this->if;
	}

	public function setIf(IRuleMl $element) {
		$this->if = $element;
	}

	public function getThen() {
		return $this->then;
	}

	public function setThen(IRuleMl $element) {
		$this->then = $element;
	}

	public function __construct(IRuleMl $if = null, IRuleMl $then = null) {
		$this->if = $if;
		$this->then = $then;
	}

	/**
	 * Create 'if' element and attach children
	 *
	 * @param array $children
	 */
	public function createIf(array $children = array()) {
		$this->if = new IfElement($children);
	}

	/**
	 * Create 'then' element and attach children
	 *
	 * @param array $children
	 */
	public function createThen(array $children = array()) {
		$this->then = new ThenElement($children);
	}

	public function getDom(\DOMDocument $document) {
		if (empty($this->if) || empty($this->then)) {
			throw new RuleMlException('Implies missing an \'if\' or \'then\'.');
		}

		$element = $document->createElementNS(RuleMl::RULML_NS, $this->getElementName());
		$element->appendChild($this->if->getDom($document));
		$element->appendChild($this->then->getDom($document));

		return $element;
	}

}

?>