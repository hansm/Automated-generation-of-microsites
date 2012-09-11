<?php
namespace UT\Hans\AutoMicrosite\Template;

use Lib\MicrodataPhp\MicrodataPhpDOMDocument;
use Lib\MicrodataPhp\MicrodataPhpDOMElement;
use UT\Hans\AutoMicrosite\RuleMl\RuleMl;

/**
 * Template handling class
 *
 * @author Hans
 */
class MicrodataTemplate {

	const RULML_NS = 'http://ruleml.org/spec';

	const SLOT_ITEMTYPE = 'http://automicrosite.maesalu.com/TemplateSlot';

	/**
	 * URL to template file
	 *
	 * @var string
	 */
	private $fileUrl;
	
	/**
	 * Template DOM object
	 * 
	 * @var \Lib\MicrodataPhp\MicrodataPhpDOMDocument 
	 */
	private $dom;
	
	/**
	 * Template file URL
	 * 
	 * @return string 
	 */
	public function getFileUrl() {
		return $this->fileUrl;
	}
	
	/**
	 * @param string $templateFileUrl 
	 * @throws \ErrorException
	 */
	public function __construct($templateFileUrl) {
		$this->fileUrl = $templateFileUrl;
		$this->dom = new MicrodataPhpDOMDocument();
		if (!$this->dom->loadHTML(\file_get_contents($this->fileUrl))) {
			throw new \ErrorException('Could not load template file.');
		}
	}
	
	public function getSlots() {
		return $this->dom->getItems(self::SLOT_ITEMTYPE);
	}
	
	/**
	 * TODO: this should be done with proper DOM
	 * @var array 
	 */
	public $headHtml = array();

	public function appendToHead($html) {
		$headElement = $this->dom->getElementsByTagName('head')->item(0);
		if (!$headElement) {
			throw new \RuntimeException('Head tag not found.');
		}
		\array_push($this->headHtml, $html);
	}

	/**
	 * Set template title tag
	 * 
	 * @param string $title 
	 * @throws \RuntimeException
	 */
	public function setTitle($title) {
		$titleElement = $this->dom->getElementsByTagName('title')->item(0);
		if (!$titleElement) {
			throw new \RuntimeException('Title tag not found.');
		}
		$titleElement->nodeValue = $title;
	}
	
	public function setSlot(MicrodataPhpDOMElement $element, $newContents) {
		$newElement = DomHtml::stringToDomElement($newContents, $element->ownerDocument);
		$element->parentNode->replaceChild($newElement, $element);
	}

	/**
	 * Get template HTML
	 * 
	 * @return string 
	 */
	public function toHtml() {
		$html = $this->dom->saveHTML();
		
		// TODO: this should be done using DOM
		$headHtml = \implode("\n", $this->headHtml);
		$html = \str_replace('</head>', "\n". $headHtml ."\n</head>", $html);
		
		return $html;
	}

	/**
	 * Get RuleML facts for RuleML engine
	 * 
	 * @return ??? 
	 */
	public function toRuleMl() {
		$transformer = new \UT\Hans\AutoMicrosite\RuleMl\MicrodataToRuleMl();
		$result = $transformer->transformTemplate($this->dom, $this->fileUrl);
		return $result;
	}

}

?>