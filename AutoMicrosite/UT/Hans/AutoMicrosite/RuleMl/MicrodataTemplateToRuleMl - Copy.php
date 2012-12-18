<?php
namespace UT\Hans\AutoMicrosite\RuleMl;

use UT\Hans\AutoMicrosite\Template\Template;
use DOMDocument;
use DOMElement;
use Lib\MicrodataPhp\MicrodataPhpDOMDocument;
use Lib\MicrodataPhp\MicrodataPhpDOMElement;
use UT\Hans\AutoMicrosite\Util\KeyValue;

/**
 * Create RuleML facts from template
 *
 * @author Hans
 */
class MicrodataTemplateToRuleMl {
	
	const RULML_NS = 'http://ruleml.org/spec';
	
	const PLACEHOLDER_ITEMTYPE = 'http://automicrosite.maesalu.com/TemplatePlaceholder';
	
	public function __construct() {
		//
	}
	
	/**
	 *
	 * @param MicrodataPhpDOMDocument $microdataDocument
	 * @param type $templateId
	 * @return \UT\Hans\AutoMicrosite\RuleMl\RuleMl
	 */
	public function transformTemplate(MicrodataPhpDOMDocument $microdataDocument, $templateId = null) {
		$document = new DOMDocument('1.0', 'UTF-8');
		
		$ruleMlElement = $document->createElementNS(self::RULML_NS, 'RuleML');
		$document->appendChild($ruleMlElement);
		
		$assertElement = $document->createElementNS(self::RULML_NS, 'Assert');
		$ruleMlElement->appendChild($assertElement);
		
		$nodeList = $microdataDocument->getItems(self::PLACEHOLDER_ITEMTYPE);
		
		$properties = array();
		for ($i = 0; $i < $nodeList->length; $i++) {
			$item = $nodeList->item($i);
			$itemId = $item->itemId();
			
			// read properties
			$properties[$itemId] = array();
			foreach ($item->properties() as $property) {
				foreach ($property->itemProp() as $propertyName) {
					if (!isset($properties[$itemId][$propertyName])) {
						$properties[$itemId][$propertyName] = array();
					}
					$properties[$itemId][$propertyName][] = $property->itemValue();
				}
			}
		}

		// create facts
		$templateImpliesElement = $document->createElementNS(self::RULML_NS, 'Implies');
		$assertElement->appendChild($templateImpliesElement);
		
		$templateIfElement = $document->createElementNS(self::RULML_NS, 'if');
		$templateImpliesElement->appendChild($templateIfElement);
		$templateIfAndElement = $document->createElementNS(self::RULML_NS, 'And');
		$templateIfElement->appendChild($templateIfAndElement);
		
		$templateThenElement = $document->createElementNS(self::RULML_NS, 'then');
		$templateImpliesElement->appendChild($templateThenElement);
		
		$this->createAtom($templateThenElement, 'http://automicrosite.maesalu.com/#template', array(
			new KeyValue('template', new KeyValue('Ind', $templateId))
		));
		
		foreach ($properties as $placeholderId => $templateProp) {
			$isOptional = isset($templateProp['optional']) && isset($templateProp['optional'][0])
							&& strcasecmp($templateProp['optional'][0], 'true') == 0;
			
			if (!empty($templateProp['category'])) {
				foreach ($templateProp['category'] as $categoryValue) {
					$this->createAtom($assertElement, 'http://automicrosite.maesalu.com/TemplatePlaceholder#category', array(
						new KeyValue('template', new KeyValue('Ind', $templateId)),
						new KeyValue('placeholder', new KeyValue('Ind', $placeholderId)),
						new KeyValue('category', new KeyValue('Ind', $categoryValue)),
					));
				}
			}

			// check that there is a widget for this placeholders
			$impliesElement = $document->createElementNS(self::RULML_NS, 'Implies');
			$assertElement->appendChild($impliesElement);

			$ifElement = $document->createElementNS(self::RULML_NS, 'if');
			$impliesElement->appendChild($ifElement);
			$andElement = $document->createElementNS(self::RULML_NS, 'And');
			$ifElement->appendChild($andElement);

			$this->createAtom($andElement, 'http://automicrosite.maesalu.com/TemplatePlaceholder#category', array(
				new KeyValue('template', new KeyValue('Ind', $templateId)),
				new KeyValue('placeholder', new KeyValue('Ind', $placeholderId)),
				new KeyValue('category', new KeyValue('Var', 'category')),
			));

			$this->createAtom($andElement, 'http://openajax.org/metadata#category', array(
				new KeyValue('widget', new KeyValue('Var', 'widget')),
				new KeyValue('category', new KeyValue('Var', 'category')),
			));

			$thenElement = $document->createElementNS(self::RULML_NS, 'then');
			$impliesElement->appendChild($thenElement);

			$relName = 'http://automicrosite.maesalu.com/#widgetPlace';
			$this->createAtom($thenElement, $relName, array(
				new KeyValue('widget', new KeyValue('Var', 'widget')),
				new KeyValue('placeholder', new KeyValue('Ind', $placeholderId)),
				new KeyValue('template', new KeyValue('Ind', $templateId))
			));
				
			if (!$isOptional) {
				$this->createAtom($templateIfAndElement, $relName, array(
					new KeyValue('widget', new KeyValue('Var', $placeholderId .'_widget')),
					new KeyValue('placeholder', new KeyValue('Ind', $placeholderId)),
					new KeyValue('template', new KeyValue('Ind', $templateId))
				));
			}
		}
		
		return RuleMl::createFromDom($document);
	}
	
	private function createAtom(DOMElement $parentElement, $rel, array $slots = array()) {
		$document = $parentElement->ownerDocument;
		
		$atomElement = $document->createElementNS(self::RULML_NS, 'Atom');
		$parentElement->appendChild($atomElement);
		
		// rel
		$relElement = $document->createElementNS(self::RULML_NS, 'Rel');
		if (stripos($rel, 'http:') === 0) {
			$relElement->setAttribute('iri', $rel);
		} else {
			$relElement->appendChild($document->createTextNode($rel));
		}
		$atomElement->appendChild($relElement);
		
		// slots
		foreach ($slots as $slot) {
			$slotElement = $document->createElementNS(self::RULML_NS, 'slot');
			$atomElement->appendChild($slotElement);
			
			$slotElement->appendChild(
				$document->createElementNS(self::RULML_NS, 'Ind', $slot->key)
			);
			$slotElement->appendChild(
				$document->createElementNS(self::RULML_NS, $slot->value->key, $slot->value->value)
			);
		}
	}
	
}

?>