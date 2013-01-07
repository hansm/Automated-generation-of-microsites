<?php
namespace UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement;

use Lib\MicrodataPhp\MicrodataPhpDOMElement;
use DOMElement;

abstract class AbstractConstant extends AbstractRuleMl {

	/**
	 * Element
	 * @var string
	 */
	private $type;

	/**
	 * IRI attribute value
	 * @var string
	 */
	private $iri;

	/**
	 * Element value
	 * @var string
	 */
	private $value;

	protected $elementName = 'Const';

	public function getType() {
		return $this->type;
	}

	public function getIri() {
		return $this->iri;
	}

	public function getValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function setIri($iri) {
		$this->iri = $iri;
	}

	public function __construct($value = null, $iri = null, $type = null) {
		$this->value = $value;
		$this->type = $type;
		$this->iri = $iri;
	}

	public function appendTo(DOMElement $parent) {
		$parent->appendChild($this->getDom($parent->ownerDocument));
	}

	public function getDom(\DOMDocument $document) {
		$element = $document->createElementNS(RuleMl::RULML_NS, $this->elementName);

		if (isset($this->iri)) {
			$element->setAttribute('iri', $this->iri);
		}
		if (isset($this->value)) {
			$element->appendChild($document->createTextNode($this->value));
		}
		if (isset($this->type)) {
			$element->setAttribute('type', $this->type);
		}

		return $element;
	}

}
