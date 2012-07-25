<?php
namespace UT\Hans\AutoMicrosite\RuleMl;

use \DOMDocument;
use \XSLTProcessor;

/**
 * Create RuleML facts from
 *
 * @author Hans
 */
class OpenAjaxToRuleMl {

	/**
	 * XSL file for transformation
	 * 
	 * @var string
	 */
	private $xslFile = 'Rules/OpenAjaxToRuleML.xsl';

	/**
	 * XSL DOM document
	 *
	 * @var \DOMDocument
	 */
	private $xslDocument;

	public function __construct() {
		$this->xslDocument = new DOMDocument('1.0', 'UTF-8');
		$this->xslDocument->load($this->xslFile);
	}

	/**
	 * Transform OpenAjax metadata file to RuleML object
	 *
	 * @param string $sourceDocumentFile
	 * @param int $widgetNumber parameter to pass to transformation
	 * @return \UT\Hans\AutoMicrosite\RuleMl\RuleMl
	 * @throws \UT\Hans\AutoMicrosite\RuleMl\RuleMlException
	 */
	public function transformFile($sourceDocumentFile, $widgetNumber = null) {
		$document = new DOMDocument('1.0', 'UTF-8');
		$document->load($sourceDocumentFile);
		return $this->transform($document, $widgetNumber);
	}

	/**
	 * Transform OpenAjax metadata string to RuleML object
	 *
	 * @param string $sourceDocumentFile
	 * @param int $widgetNumber parameter to pass to transformation
	 * @return \UT\Hans\AutoMicrosite\RuleMl\RuleMl
	 * @throws \UT\Hans\AutoMicrosite\RuleMl\RuleMlException
	 */
	public function transformString($sourceDocumentString, $widgetNumber = null) {
		$document = new DOMDocument('1.0', 'UTF-8');
		if ($document->loadXML($sourceDocumentString) === false) {
			throw new RuleMlException('Could not parse XML.');
		}
		return $this->transform($document, $widgetNumber);
	}

	/**
	 * Transform DOM document into RuleML object
	 *
	 * @param \DOMDocument $sourceDocument
	 * @param int $widgetNumber parameter to pass to transformation
	 * @return \UT\Hans\AutoMicrosite\RuleMl\RuleMl
	 * @throws \UT\Hans\AutoMicrosite\RuleMl\RuleMlException
	 */
	public function transform(DOMDocument $sourceDocument, $widgetNumber = null) {
		$xsltProcessor = new XSLTProcessor();
		$xsltProcessor->setParameter('', 'widget', $widgetNumber);
		$xsltProcessor->importStyleSheet($this->xslDocument);
		$result = $xsltProcessor->transformToDoc($sourceDocument);
		if ($result === false) {
			throw new RuleMlException('Could not transform document.');
		}
		return RuleMl::createFromDom($result);
	}

}

?>