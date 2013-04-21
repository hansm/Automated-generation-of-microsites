<?php
namespace UT\Hans\AutoMicrosite\RuleGenerator;

use RuntimeException;
use UT\Hans\AutoMicrosite\Template\Template;
use DOMDocument;
use DOMElement;
use Lib\MicrodataPhp\MicrodataPhpDOMDocument;
use Lib\MicrodataPhp\MicrodataPhpDOMElement;

use UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement\RuleMl AS RuleMlElement;
use UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement\Assert;
use UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement\Implies;
use UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement\AndElement;
use UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement\Slot;
use UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement\Ind;
use UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement\Rel;
use UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement\Atom;
use UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement\Variable;
use UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement\Type as RuleMlType;

/**
 * Create RuleML facts from template
 *
 * @author Hans
 */
class MicrodataTemplateToRuleMl {

	const RULML_NS = 'http://ruleml.org/spec';

	/**
	 * Layout template placeholder type
	 */
	const PLACEHOLDER_ITEMTYPE = 'http://deepweb.ut.ee/TemplatePlaceholder';

	/**
	 * Template placeholder dimension limits
	 *
	 * @var array
	 */
	private $dimensionVariables = array('min-height', 'min-width', 'max-height', 'max-width');

	/**
	 * Generate template rules
	 *
	 * @param string $file
	 * @param string $templateId
	 * @return string
	 * @throws \RuntimeException
	 */
	public function transformFile($file, $templateId) {
		$document = new MicrodataPhpDOMDocument();
		if (!$document->loadHTML(\file_get_contents($file))) {
			throw new RuntimeException('Could not load template file.');
		}
		return $this->transformTemplate($document, $templateId);
	}

	/**
	 *
	 * @param MicrodataPhpDOMDocument $microdataDocument
	 * @param string $templateId
	 * @return string
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
			self::createAtom(Relation::TEMPLATE_WIDGETS, array(
				new Slot(new Ind('template'), new Ind($templateId))
			))
		));

		// Template exists
		$assert->appendChild(
			self::createAtom(Relation::TEMPLATE, array(new Ind($templateId)))
		);

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
						self::createAtom(Relation::getTemplateDimensionsRel($dimensionVar), array(
							new Slot(new Ind('template'), new Ind($templateId)),
							new Slot(new Ind('placeholder'), new Ind($placeholderId)),
							new Slot(
								new Ind($dimensionVar),
								new Ind($templateProp[$dimensionVar][0], null, RuleMlType::INTEGER)
							)
						))
					);
				}
			}

			// Allows multiple widgets
			if (isset($templateProp['multiple']) && isset($templateProp['multiple'][0])
					&& strcasecmp($templateProp['multiple'][0], 'true') == 0) {
				$assert->appendChild(
					self::createAtom(Relation::TEMPLATE_MULTIPLE, array(
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
				self::createAtom(Relation::PLACE_CATEGORY, array(
					new Slot(new Ind('template'), new Ind($templateId)),
					new Slot(new Ind('placeholder'), new Ind($placeholderId)),
					new Slot(new Ind('category'), new Variable('category'))
				))
			);
			$ifAnd->appendChild(
				self::createAtom(Relation::WIDGET_CATEGORY, array(
					new Slot(new Ind('widget'), new Variable('widget')),
					new Slot(new Ind('category'), new Variable('category'))
				))
			);
			$ifAnd->appendChild(
				self::createAtom(Relation::WIDGET_IS_DATA, array(
					new Slot(new Ind('widget'), new Variable('widget')),
					new Slot(new Ind('value'), new Ind('false'))
				))
			);
			$implies->createIf(array($ifAnd));

			$implies->createThen();
			$implies->getThen()->appendChild(
				self::createAtom(Relation::WIDGET_PLACE, array(
					new Slot(new Ind('widget'), new Variable('widget')),
					new Slot(new Ind('placeholder'), new Ind($placeholderId)),
					new Slot(new Ind('template'), new Ind($templateId)),
				))
			);

			if (!$isOptional) {
				$templateIfAnd->appendChild(
					self::createAtom(Relation::WIDGET_PLACE, array(
						new Slot(new Ind('widget'), new Variable($placeholderId .'_widget')),
						new Slot(new Ind('placeholder'), new Ind($placeholderId)),
						new Slot(new Ind('template'), new Ind($templateId)),
					))
				);
			}
		}

		return $ruleMl->getDomDocument()->saveXML();
	}

	/**
	 * Create category facts
	 *
	 * @param array $categories
	 * @param string $templateId
	 * @param string $placeholderId
	 * @return \UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement\Atom[]
	 */
	private static function createCategoryFacts(array $categories, $templateId, $placeholderId) {
		$facts = array();
		foreach ($categories as $categoryValue) {
			$facts[] = self::createAtom(Relation::PLACE_CATEGORY, array(
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
	 * @return \UT\Hans\AutoMicrosite\RuleGenerator\RuleMlElement\Atom
	 */
	private static function createAtom($rel, array $slots = array()) {
		$atom = new Atom();
		$atom->appendChild(new Rel($rel));
		foreach ($slots as $slot) {
			$atom->appendChild($slot);
		}

		return $atom;
	}

	/**
	 * Get placeholder properties from DOM document
	 *
	 * @param \Lib\MicrodataPhp\MicrodataPhpDOMDocument $microdataDocument
	 * @return array
	 */
	public static function getPlaceholders(MicrodataPhpDOMDocument $microdataDocument) {
		$nodeList = $microdataDocument->getItems(self::PLACEHOLDER_ITEMTYPE); // TODO: maybe I should also go through all properties to find placeholders that are properties of other items???
		$properties = array();
		for ($i = 0; $i < $nodeList->length; $i++) {
			$item = $nodeList->item($i);
			$itemId = $item->itemId();
			// TODO: generate itemid-s when there is none in the template

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

}
