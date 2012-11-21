<?php
namespace UT\Hans\AutoMicrosite\Template;

use Lib\MicrodataPhp\MicrodataPhpDOMDocument;
use Lib\MicrodataPhp\MicrodataPhpDOMElement;
use UT\Hans\AutoMicrosite\RuleMl\RuleMl;
use UT\Hans\AutoMicrosite\RuleMl\MicrodataTemplateToRuleMl;

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
	 * @return \UT\Hans\AutoMicrosite\RuleMl\RuleMl
	 */
	public function toRuleMl() {
		$transformer = new MicrodataTemplateToRuleMl();
		$result = $transformer->transformTemplate($this->dom, $this->fileUrl);
		return $result;
	}
	
	/**
	 * Get JSON data about the template
	 * TODO: possibly do this on client side since placeholder data is still available there
	 */
	public function getJson() {
		$templateData = MicrodataTemplateToRuleMl::getPlaceholders($this->dom);
		foreach ($templateData as &$placeHolder) {
			$placeHolder['multiple'] = isset($placeHolder['multiple'])
				&& isset($placeHolder['multiple'][0])
				&& strcasecmp($placeHolder['multiple'][0], 'true') === 0;

			$placeHolder['optional'] = isset($placeHolder['optional'])
				&& isset($placeHolder['optional'][0])
				&& strcasecmp($placeHolder['optional'][0], 'true') === 0;
		}
		return \json_encode($templateData);
	}

}

?>