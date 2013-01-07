<?php
namespace UT\Hans\AutoMicrosite;

use Lib\MicrodataPhp\MicrodataPhpDOMDocument;
use Lib\MicrodataPhp\MicrodataPhpDOMElement;
use UT\Hans\AutoMicrosite\RuleMl\MicrodataTemplateToRuleMl;
use RuntimeException;
use UT\Hans\AutoMicrosite\RuleGenerator\ITemplate as RuleGeneratorTemplate;

/**
 * Template handling class
 */
class Template implements RuleGeneratorTemplate {

	const RULML_NS = 'http://ruleml.org/spec';

	const SLOT_ITEMTYPE = 'http://automicrosite.maesalu.com/TemplateSlot';

	/**
	 * URL to template file
	 *
	 * @var string
	 */
	private $fileUrl;

	/**
	 * HTML code to append to head
	 *
	 * @var string[]
	 */
	private $headHtml = array();

	/**
	 * Title of the page
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Template file URL
	 *
	 * @return string
	 */
	public function getFileUrl() {
		return $this->fileUrl;
	}

	/**
	 * Set title of the Web page
	 *
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Get the title of the Web site
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $templateFileUrl
	 * @throws \ErrorException
	 */
	public function __construct($templateFileUrl) {
		$this->fileUrl = $templateFileUrl;
	}

	public function getId() {
		return $this->getFileUrl();
	}

	public function getUrl() {
		return $this->getFileUrl();
	}

	public function appendToHead($html) {
		$this->headHtml[] = $html;
	}
/*
	public function setSlot(MicrodataPhpDOMElement $element, $newContents) {
		$newElement = DomHtml::stringToDomElement($newContents, $element->ownerDocument);
		$element->parentNode->replaceChild($newElement, $element);
	}*/

	/**
	 * Get RuleML facts for RuleML engine
	 *
	 * @return \UT\Hans\AutoMicrosite\RuleMl\RuleMl
	 *
	public function toRuleMl() {
		$transformer = new MicrodataTemplateToRuleMl();
		$result = $transformer->transformTemplate($this->dom, $this->fileUrl);
		return $result;
	}

	/**
	 * Get JSON data about the template
	 * TODO: possibly do this on client side since placeholder data is still available there
	 *
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
*/


	public function getJson() {
		// TODO: implement, possibly on client side instead
		return \json_encode(array());
	}

	/**
	 * Get template HTML
	 *
	 * @return string
	 * @throws \RuntimeException
	 */
	public function toHtml() {
		$document = new MicrodataPhpDOMDocument();
		if (!$document->loadHTML(\file_get_contents($this->fileUrl))) {
			throw new RuntimeException('Could not load template file.');
		}

		$titleElement = $document->getElementsByTagName('title')->item(0);
		if (!$titleElement) {
			throw new RuntimeException('Title tag not found.');
		}
		$titleElement->nodeValue = $this->getTitle();

		$html = $document->saveHTML();

		// TODO: this should be done using DOM
		$headHtml = \implode("\n", $this->headHtml);
		$html = \str_replace('</head>', "\n". $headHtml ."\n</head>", $html);

		return $html;
	}

	/**
	 * Get list of template files
	 *
	 * @param \UT\Hans\AutoMicrosite\Template[] $directory
	 * @throws \RuntimeException
	 */
	public static function getAllTemplateFiles($directory) {
		if (\substr($directory, -1) != '/') {
			$directory .= '/';
		}

		$templates = array();
		$templatesDir = \dir($directory);
		if (!$templatesDir) {
			throw new RuntimeException('Could not load templates.');
		}
		while ($file = $templatesDir->read()) {
			$a = \explode('.', $file);
			$fileType = \end($a);
			if (!empty($fileType) && \strcasecmp($fileType, 'html') == 0) {
				$templates[] = new Template($directory . $file);
			}
		}
		$templatesDir->close();
		return $templates;
	}

}
