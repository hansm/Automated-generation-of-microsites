<?php
namespace UT\Hans\AutoMicrosite\RuleMl;

use UT\Hans\AutoMicrosite\Template\Template;
use DOMDocument;
use Lib\MicrodataPhp\MicrodataPhpDOMDocument;
use Lib\MicrodataPhp\MicrodataPhpDOMElement;

/**
 * Create RuleML facts from template
 *
 * @author Hans
 */
class MicrodataToRuleMl {
	
	const RULML_NS = 'http://ruleml.org/spec';
	
	const SLOT_ITEMTYPE = 'http://automicrosite.maesalu.com/TemplateSlot';
	
	public function __construct() {
		//
	}
	
	public function transformTemplate(MicrodataPhpDOMDocument $microdataDocument, $metadataUrl = null) {
		$document = new DOMDocument('1.0', 'UTF-8');
		$ruleMlElement = $document->createElementNS(self::RULML_NS, 'RuleML');
		$assertElement = $document->createElementNS(self::RULML_NS, 'Assert');
		$ruleMlElement->appendChild($assertElement);
		
		$nodeList = $this->getTemplateSlots($microdataDocument);
		for ($i = 0; $i < $nodeList->length; $i++) {
			$atomElement = $document->createElementNS(self::RULML_NS, 'Atom');
			
			var_dump($nodeList->item($i)->itemType());
			var_dump($nodeList->item($i)->itemId());
			var_dump($nodeList->item($i)->properties());
		}
	}
	
	private function getTemplateSlots(MicrodataPhpDOMDocument $microdataDocument) {
		return $microdataDocument->getItems(self::SLOT_ITEMTYPE);
	}
	
	private function getCategoryElements(MicrodataPhpDOMElement $element) {
		//
	}
	
}

?>
