<?php
namespace UT\Hans\AutoMicrosite\RuleMl\Element;

use UT\Hans\AutoMicrosite\RuleMl\RuleMlException;

class Slot extends AbstractRuleMl {
	
	protected $elementName = 'slot';
	
	/**
	 *
	 * @var \UT\Hans\AutoMicrosite\RuleMl\Element\Ind
	 */
	private $key;

	/**
	 *
	 * @var \UT\Hans\AutoMicrosite\RuleMl\Element\Constant 
	 */
	private $value;
	
	public function setKey(Ind $key) {
		$this->key = $key;
	}
	
	public function setValue(AbstractConstant $value) {
		$this->value = $value;
	}
	
	public function __construct(Ind $key = null, AbstractConstant $value = null) {
		$this->key = $key;
		$this->value = $value;
	}
	
	public function getDom(\DOMDocument $document) {
		if (empty($this->value) || empty($this->key)) {
			throw new RuleMlException('Slot missing a key or a value element.');
		}
		
		$element = $document->createElementNS(RuleMl::RULML_NS, $this->getElementName());

		$element->appendChild($this->key->getDom($document));
		$element->appendChild($this->value->getDom($document));

		return $element;
	}
	
}

?>
