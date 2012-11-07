<?php
namespace UT\Hans\AutoMicrosite\RuleMl\Element;

class Atom extends RuleMl {
	
	protected $elementName = 'Atom';
	
	private $children = array();
	
	public function getChildren() {
		return $this->children;
	}
	
	public function __construct(array $children = array()) {
		$this->children = $children;
	}
	
	public function appendChild(RuleMl $element) {
		$this->children[] = $element;
	}
	
	public function getDom(\DOMDocument $document) {
		$element = $document->createElementNS(RuleMl::RULML_NS, $this->getElementName());

		foreach ($this->getChildren() as $child) {
			$element->appendChild($child->getDom($document));
		}

		return $element;
	}
	
}

?>
