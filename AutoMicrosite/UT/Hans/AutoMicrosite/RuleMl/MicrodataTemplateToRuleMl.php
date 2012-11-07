<?php
namespace UT\Hans\AutoMicrosite\RuleMl;

use UT\Hans\AutoMicrosite\Template\Template;
use DOMDocument;
use DOMElement;
use Lib\MicrodataPhp\MicrodataPhpDOMDocument;
use Lib\MicrodataPhp\MicrodataPhpDOMElement;

use UT\Hans\AutoMicrosite\RuleMl\Element\Slot;
use UT\Hans\AutoMicrosite\RuleMl\Element\Ind;
use UT\Hans\AutoMicrosite\RuleMl\Element\Rel;
use UT\Hans\AutoMicrosite\RuleMl\Element\Atom;
use UT\Hans\AutoMicrosite\RuleMl\Element\Variable;

/**
 * Create RuleML facts from template
 *
 * @author Hans
 */
class MicrodataTemplateToRuleMl {
	
	const RULML_NS = 'http://ruleml.org/spec';
	
	const PLACEHOLDER_ITEMTYPE = 'http://automicrosite.maesalu.com/TemplatePlaceholder';
	
	private $dimensionVariables = array('min-height', 'min-width', 'max-height', 'max-height');
	
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
			new Slot(new Ind('template'), new Ind($templateId))
		));
		
		foreach ($properties as $placeholderId => $templateProp) {
			$isOptional = isset($templateProp['optional']) && isset($templateProp['optional'][0])
							&& strcasecmp($templateProp['optional'][0], 'true') == 0;
			
			// Category limits
			if (!empty($templateProp['category'])) {
				$this->createCategoryFacts($templateProp['category'], $assertElement, $templateId, $placeholderId);
			}
			
			// Dimension limits
			foreach ($this->dimensionVariables as $dimensionVar) {
				if (!empty($templateProp[$dimensionVar])) {
					$this->createAtom($assertElement, 'http://automicrosite.maesalu.com/TemplatePlaceholder#'. $dimensionVar, array(
						new Slot(new Ind('template'), new Ind($templateId)),
						new Slot(new Ind('placeholder'), new Ind($placeholderId)),
						new Slot(
							new Ind($dimensionVar),
							new Ind($templateProp[$dimensionVar][0], null, Element\Type::INTEGER)
						)
					));
				}
			}
			
			// Allows multiple widgets
			if (isset($templateProp['multiple']) && isset($templateProp['multiple'][0])
					&& strcasecmp($templateProp['multiple'][0], 'true') == 0) {
				$this->createAtom($assertElement, 'http://automicrosite.maesalu.com/TemplatePlaceholder#multiple', array(
					new Slot(new Ind('template'), new Ind($templateId)),
					new Slot(new Ind('placeholder'), new Ind($placeholderId))
				));
			}
			
			// Check that there is a widget for this placeholders
			$impliesElement = $document->createElementNS(self::RULML_NS, 'Implies');
			$assertElement->appendChild($impliesElement);

			$ifElement = $document->createElementNS(self::RULML_NS, 'if');
			$impliesElement->appendChild($ifElement);
			$andElement = $document->createElementNS(self::RULML_NS, 'And');
			$ifElement->appendChild($andElement);

			$this->createAtom($andElement, 'http://automicrosite.maesalu.com/TemplatePlaceholder#category', array(
				new Slot(new Ind('template'), new Ind($templateId)),
				new Slot(new Ind('placeholder'), new Ind($placeholderId)),
				new Slot(new Ind('category'), new Variable('category'))
			));
			
			$this->createAtom($andElement, 'http://openajax.org/metadata#category', array(
				new Slot(new Ind('widget'), new Variable('widget')),
				new Slot(new Ind('category'), new Variable('category'))
			));
			
			$thenElement = $document->createElementNS(self::RULML_NS, 'then');
			$impliesElement->appendChild($thenElement);
			
			$relName = 'http://automicrosite.maesalu.com/#widgetPlace';
			$this->createAtom($thenElement, $relName, array(
				new Slot(new Ind('widget'), new Variable('widget')),
				new Slot(new Ind('placeholder'), new Ind($placeholderId)),
				new Slot(new Ind('template'), new Ind($templateId))
			));
			
			if (!$isOptional) {
				$this->createAtom($templateIfAndElement, $relName, array(
					new Slot(new Ind('widget'), new Variable($placeholderId .'_widget')),
					new Slot(new Ind('placeholder'), new Ind($placeholderId)),
					new Slot(new Ind('template'), new Ind($templateId))
				));
			}
		}
		
		return RuleMl::createFromDom($document);
	}
	
	private function createCategoryFacts($categories, $parent, $templateId, $placeholderId) {
		foreach ($categories as $categoryValue) {
			$this->createAtom($parent, 'http://automicrosite.maesalu.com/TemplatePlaceholder#category', array(
				new Slot(new Ind('template'), new Ind($templateId)),
				new Slot(new Ind('placeholder'), new Ind($placeholderId)),
				new Slot(new Ind('category'), new Ind($categoryValue))
			));
		}
	}
	
	private function createAtom(DOMElement $parentElement, $rel, array $slots = array()) {
		$atom = new Atom();
		
		$relElement = new Rel();
		if (stripos($rel, 'http:') === 0) {
			$relElement->setIri($rel);
		} else {
			$relElement->setValue($rel);
		}
		$atom->appendChild($relElement);
		
		foreach ($slots as $slot) {
			$atom->appendChild($slot);
		}
		
		$document = $parentElement->ownerDocument;
		$parentElement->appendChild($atom->getDom($document));
	}
	
}

?>