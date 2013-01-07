<?php
namespace UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement;

use RuntimeException;

class Slot extends AbstractRuleMl {

	protected $elementName = 'slot';

	/**
	 *
	 * @var \UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement\Ind
	 */
	private $key;

	/**
	 *
	 * @var \UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement\Constant
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
			throw new RuntimeException('Slot missing a key or a value element.');
		}

		$element = $document->createElementNS(RuleMl::RULML_NS, $this->getElementName());

		$element->appendChild($this->key->getDom($document));
		$element->appendChild($this->value->getDom($document));

		return $element;
	}

}
