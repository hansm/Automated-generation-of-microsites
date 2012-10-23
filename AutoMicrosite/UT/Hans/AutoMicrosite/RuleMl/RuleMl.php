<?php
namespace UT\Hans\AutoMicrosite\RuleMl;

use ErrorException;
use DOMDocument;

/**
 * RuleML class
 *
 * @author Hans
 */
class RuleMl {

	/**
	 * RuleML XML namespace
	 */
	const RULEML_NS = 'http://ruleml.org/spec';

	/**
	 * RuleML ruleset document
	 * 
	 * @var \DOMDocument
	 */
	protected $ruleset;

	/**
	 * Return ruleset as DOM document
	 *
	 * @return \DOMDocument
	 */
	public function getDom() {
		return $this->ruleset;
	}

	/**
	 * Return ruleset as a string
	 *
	 * @return string
	 * @throws \ErrorException
	 */
	public function getString() {
		$ruleset = $this->ruleset->saveXML();
		if ($ruleset === false) {
			throw new ErrorException('Could not parse XML.');
		}
		return trim($ruleset);
	}


	/**
	 * Set ruleset from string
	 * 
	 * @param string $ruleset
	 * @throws \ErrorException
	 */
	public function setFromString($ruleset) {
		$document = new DOMDocument();
		if ($document->loadXML($ruleset) === false) {
			throw new ErrorException('Could not parse XML.');
		}
		$this->ruleset = $document;
	}

	/**
	 * Set ruleset from DOM document
	 *
	 * @param \DOMDocument $document
	 */
	public function setFromDom(DOMDocument $document) {
		$this->ruleset = $document;
	}

	/**
	 * Merge second RuleML into this one
	 *
	 * @param \UT\Hans\AutoMicrosite\RuleMl\RuleMl $otherRuleMl
	 * @throws \DOMException
	 */
	public function merge(RuleMl $otherRuleMl) {
		if (!$otherRuleMl->getDom()->hasChildNodes()) {
			return;
		}
		if (!$this->getDom() || !$this->getDom()->hasChildNodes()
				|| ($parentElement = $this->getDom()->getElementsByTagNameNS(self::RULEML_NS, 'Assert')->item(0)) === null) {
			// this document is empty, so take the other
			$this->setFromDom($otherRuleMl->getDom()->cloneNode(true));
			return;
		}

		$otherParentElement = $otherRuleMl->getDom()->getElementsByTagNameNS(self::RULEML_NS, 'Assert')->item(0);
		if ($otherParentElement === null || !$otherParentElement->hasChildNodes()) {
			return;
		}

		$children = $otherParentElement->childNodes;
		for ($i = 0; $i < $children->length; $i++) {
			$element = $this->getDom()->importNode($children->item($i), true);
			$parentElement->appendChild($element);
		}
	}

	/**
	 * Create RuleML object from RuleML string
	 *
	 * @param string $ruleset
	 * @return \UT\Hans\AutoMicrosite\Util\RuleMl\RuleMl
	 * @throws \ErrorException
	 */
	public static function createFromString($ruleset) {
		$ruleMl = new RuleMl();
		$ruleMl->setFromString($ruleset);
		return $ruleMl;
	}

	/**
	 * Create RuleML object from RuleML DOM document
	 *
	 * @param DOMDocument $document
	 * @return \UT\Hans\AutoMicrosite\Util\RuleMl\RuleMl
	 */
	public static function createFromDom(DOMDocument $document) {
		$ruleMl = new RuleMl();
		$ruleMl->setFromDom($document);
		return $ruleMl;
	}

}

?>