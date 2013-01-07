<?php
namespace UT\Hans\AutoMicrosite\RuleGenerator;

use DOMDocument;
use XSLTProcessor;
use RuntimeException;

/**
 * Create RuleML facts from
 */
class OpenAjaxToRuleMl {

	/**
	 * XSL file for transformation
	 *
	 * @var string
	 */
	const XSL_FILE = 'Rules/OpenAjaxToRuleML.xsl';

	/**
	 * XSL DOM document
	 *
	 * @var \DOMDocument
	 */
	private $xslDocument;

	public function __construct() {
		$this->xslDocument = new DOMDocument('1.0', 'UTF-8');
		$this->xslDocument->load(self::XSL_FILE);
	}

	/**
	 * Transform OpenAjax metadata file to RuleML object
	 *
	 * @param string $sourceDocumentFile
	 * @param int $widgetId parameter to pass to transformation
	 * @return string
	 * @throws \RuntimeException
	 */
	public function transformFile($sourceDocumentFile, $widgetId = null) {
		$document = new DOMDocument('1.0', 'UTF-8');
		$document->load($sourceDocumentFile);
		return $this->transform($document, $widgetId);
	}

	/**
	 * Transform OpenAjax metadata string to RuleML object
	 *
	 * @param string $sourceDocumentFile
	 * @param int $widgetId parameter to pass to transformation
	 * @return string
	 * @throws \RuntimeException
	 */
	public function transformString($sourceDocumentString, $widgetId = null) {
		$document = new DOMDocument('1.0', 'UTF-8');
		if ($document->loadXML($sourceDocumentString) === false) {
			throw new RuleMlException('Could not parse XML.');
		}
		return $this->transform($document, $widgetId);
	}

	/**
	 * Transform DOM document into RuleML object
	 *
	 * @param \DOMDocument $sourceDocument
	 * @param string $widgetId parameter to pass to transformation
	 * @return string
	 * @throws \RuntimeException
	 */
	public function transform(DOMDocument $sourceDocument, $widgetId = null) {
		$xsltProcessor = new XSLTProcessor();
		$xsltProcessor->setParameter('', 'widget', $widgetId);
		$xsltProcessor->importStyleSheet($this->xslDocument);
		$result = $xsltProcessor->transformToDoc($sourceDocument);
		if ($result === false) {
			throw new RuntimeException('Could not transform document.');
		}
		return $result->saveXML();
	}

}
