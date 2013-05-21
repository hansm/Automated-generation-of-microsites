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

	const SLOT_ITEMTYPE = 'http://deepweb.ut.ee/TemplateSlot';

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

	/**
	 * Append contents to the end of head tag
	 *
	 * @param string $html
	 */
	public function appendToHead($html) {
		$this->headHtml[] = $html;
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
