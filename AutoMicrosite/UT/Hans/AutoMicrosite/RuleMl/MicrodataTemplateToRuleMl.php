<?php
namespace UT\Hans\AutoMicrosite\RuleMl;

use UT\Hans\AutoMicrosite\Template\Template;
use DOMDocument;
use DOMElement;
use Lib\MicrodataPhp\MicrodataPhpDOMDocument;
use Lib\MicrodataPhp\MicrodataPhpDOMElement;

use UT\Hans\AutoMicrosite\RuleMl\Element\RuleMl AS RuleMlElement;
use UT\Hans\AutoMicrosite\RuleMl\Element\Assert;
use UT\Hans\AutoMicrosite\RuleMl\Element\Implies;
use UT\Hans\AutoMicrosite\RuleMl\Element\AndElement;
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

	const PLACEHOLDER_ITEMTYPE = 'http://deepweb.ut.ee/TemplatePlaceholder';

	/**
	 * Template fits facts relation
	 */
	const TEMPLATE_REL = 'http://deepweb.ut.ee/#template';
	
	const WIDGET_PLACE_REL = 'http://deepweb.ut.ee/#widgetPlace';

	private $dimensionVariables = array('min-height', 'min-width', 'max-height', 'max-height');

	public function __construct() {
		//
	}
	
	/**
	 * Get placeholder properties from DOM document
	 * 
	 * @param \Lib\MicrodataPhp\MicrodataPhpDOMDocument $microdataDocument
	 * @return type
	 */
	public static function getPlaceholders(MicrodataPhpDOMDocument $microdataDocument) {
		$nodeList = $microdataDocument->getItems(self::PLACEHOLDER_ITEMTYPE);
		$properties = array();
		for ($i = 0; $i < $nodeList->length; $i++) {
			$item = $nodeList->item($i);
			$itemId = $item->itemId();

			// Read properties
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

		return $properties;
	}

	/**
	 *
	 * @param MicrodataPhpDOMDocument $microdataDocument
	 * @param type $templateId
	 * @return \UT\Hans\AutoMicrosite\RuleMl\RuleMl
	 */
	public function transformTemplate(MicrodataPhpDOMDocument $microdataDocument, $templateId = null) {
		$properties = self::getPlaceholders($microdataDocument);

		$ruleMl = new RuleMlElement();
		$assert = new Assert();
		$ruleMl->appendChild($assert);

		// Create facts
		// Template matches widgets relation
		$templateImplies = new Implies();
		$assert->appendChild($templateImplies);
		
		$templateIfAnd = new AndElement();
		$templateImplies->createIf(array($templateIfAnd));
		
		$templateImplies->createThen(array(
			self::createAtom(self::TEMPLATE_REL, array(
				new Slot(new Ind('template'), new Ind($templateId))
			))
		));

		foreach ($properties as $placeholderId => $templateProp) {
			$isOptional = isset($templateProp['optional']) && isset($templateProp['optional'][0])
							&& strcasecmp($templateProp['optional'][0], 'true') == 0;

			// Category limits
			if (!empty($templateProp['category'])) {
				$assert->appendChildren(
					self::createCategoryFacts($templateProp['category']
						, $templateId, $placeholderId)
				);
			}

			// Dimension limits
			foreach ($this->dimensionVariables as $dimensionVar) {
				if (!empty($templateProp[$dimensionVar])) {
					$assert->appendChild(
						self::createAtom('http://deepweb.ut.ee/TemplatePlaceholder#'. $dimensionVar, array(
							new Slot(new Ind('template'), new Ind($templateId)),
							new Slot(new Ind('placeholder'), new Ind($placeholderId)),
							new Slot(
								new Ind($dimensionVar),
								new Ind($templateProp[$dimensionVar][0], null, Element\Type::INTEGER)
							)
						))
					);
				}
			}

			// Allows multiple widgets
			if (isset($templateProp['multiple']) && isset($templateProp['multiple'][0])
					&& strcasecmp($templateProp['multiple'][0], 'true') == 0) {
				$assert->appendChild(
					self::createAtom('http://deepweb.ut.ee/TemplatePlaceholder#multiple', array(
						new Slot(new Ind('template'), new Ind($templateId)),
						new Slot(new Ind('placeholder'), new Ind($placeholderId))
					))
				);
			}

			// Check that there is a widget for this placeholders
			$implies = new Implies();
			$assert->appendChild($implies);

			$ifAnd = new AndElement();
			$ifAnd->appendChild(
				self::createAtom('http://deepweb.ut.ee/TemplatePlaceholder#category', array(
					new Slot(new Ind('template'), new Ind($templateId)),
					new Slot(new Ind('placeholder'), new Ind($placeholderId)),
					new Slot(new Ind('category'), new Variable('category'))
				))
			);
			$ifAnd->appendChild(
				self::createAtom('http://openajax.org/metadata#category', array(
					new Slot(new Ind('widget'), new Variable('widget')),
					new Slot(new Ind('category'), new Variable('category'))
				))
			);
			$ifAnd->appendChild(
				self::createAtom('http://deepweb.ut.ee/#isDataWidget', array(
					new Slot(new Ind('widget'), new Variable('widget')),
					new Slot(new Ind('value'), new Variable('isDataWidget'))
				))
			);
			$ifAnd->appendChild(
				self::createAtom('http://deepweb.ut.ee/#priority', array(
					new Slot(new Ind('widget'), new Variable('widget')),
					new Slot(new Ind('priority'), new Variable('priority'))
				))
			);
			$implies->createIf(array($ifAnd));

			$implies->createThen();
			$implies->getThen()->appendChild(
				self::createAtom(self::WIDGET_PLACE_REL, array(
					new Slot(new Ind('widget'), new Variable('widget')),
					new Slot(new Ind('placeholder'), new Ind($placeholderId)),
					new Slot(new Ind('template'), new Ind($templateId)),
					new Slot(new Ind('isDataWidget'), new Variable('isDataWidget')),
					new Slot(new Ind('priority'), new Variable('priority'))
				))
			);

			if (!$isOptional) {
				$templateIfAnd->appendChild(
					self::createAtom(self::WIDGET_PLACE_REL, array(
						new Slot(new Ind('widget'), new Variable($placeholderId .'_widget')),
						new Slot(new Ind('placeholder'), new Ind($placeholderId)),
						new Slot(new Ind('template'), new Ind($templateId))
					))
				);
			}
		}

		return RuleMl::createFromDom($ruleMl->getDomDocument());
	}

	/**
	 * Create category facts
	 * 
	 * @param array $categories
	 * @param string $templateId
	 * @param string $placeholderId
	 * @return array
	 */
	private static function createCategoryFacts(array $categories, $templateId, $placeholderId) {
		$facts = array();
		foreach ($categories as $categoryValue) {
			$facts[] = self::createAtom('http://deepweb.ut.ee/TemplatePlaceholder#category', array(
				new Slot(new Ind('template'), new Ind($templateId)),
				new Slot(new Ind('placeholder'), new Ind($placeholderId)),
				new Slot(new Ind('category'), new Ind($categoryValue))
			));
		}
		return $facts;
	}

	/**
	 * Create Atom element
	 *
	 * @return \UT\Hans\AutoMicrosite\RuleMl\Element\Atom
	 */
	private static function createAtom($rel, array $slots = array()) {
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

		return $atom;
	}

}

?>