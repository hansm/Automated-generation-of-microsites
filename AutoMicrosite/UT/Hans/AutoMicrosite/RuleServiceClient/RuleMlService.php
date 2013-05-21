<?php
namespace UT\Hans\AutoMicrosite\RuleServiceClient;

use RuntimeException;
use ErrorException;
use DOMNode;
use DOMDocument;
use UT\Hans\AutoMicrosite\Util\Log;

/**
 * OO jDREW RuleML service client
 */
class RuleMlService extends AbstractClient {

	/**
	 * RuleML XML namespace
	 */
	const RULEML_NS = 'http://ruleml.org/spec';

	/**
	 * Unique ruleset ID from RuleML service
	 *
	 * @var int
	 */
	private $rulesetId;

	/**
	 * cURL based client implementation
	 *
	 * @var \UT\Hans\AutoMicrosite\RuleServiceClient\RuleMlServiceClient
	 */
	private $curlClient;

	public function __construct($url, $ruleset, $templateQuery, $widgetQuery) {
		parent::__construct($url, $ruleset, $templateQuery, $widgetQuery);
		$this->curlClient = new RuleMlServiceClient($this->getUrl());
		try {
			$this->rulesetId = $this->curlClient->create($ruleset);
		} catch (Exception $e) {
			$log = new Log('system');
			$log->exception($e);
			throw new RuntimeException('Rule service connection problem.');
		}
	}

	public function getTemplate() {
		try {
			$response = $this->curlClient->query($this->rulesetId
							, $this->getTemplateQuery());

			$template = null;
			$variables = self::getResponseVarNodes($response);
			for ($i = 0; $i < $variables->length; $i++) {
				$var = $variables->item($i);
				switch (self::getResponseVarName($var)) {
					case 'template':
						$template = self::getResponseVarValue($var);
						break;
				}
			}

			if (!isset($template)) {
				throw new RuntimeException('No template selected.');
			}

			return $template;
		} catch (ErrorException $e) {
			throw new RuntimeException('Rule service connection problem.');
		}
	}

	public function getWidgetInfo($widget, $template) {
		try {
			$response = $this->curlClient->query($this->rulesetId
							, $this->getWidgetQuery($widget, $template));

			return new RuleMlWidget($response);
		} catch (ErrorException $e) {
			throw new RuntimeException('Rule service connection problem.');
		}
	}

	/**
	 * Parse response RuleML to get variable value
	 *
	 * @param \DOMNode $node
	 * @return string
	 */
	public static function getResponseVarValue(DOMNode $node) {
		$value = \explode(':', $node->nextSibling->nodeValue);
		$value = \trim(\reset($value));
		return \trim($value, '"');
	}

	/**
	 * Parse response RuleML to get variable name
	 *
	 * @param \DOMNode $node
	 * @return string
	 */
	public static function getResponseVarName(DOMNode $node) {
		return \trim($node->nodeValue);
	}

	/**
	 * Get variable DOM nodes from response message
	 *
	 * @param string $response
	 * @return \DOMNodeList
	 * @throws \RuntimeException
	 */
	public static function getResponseVarNodes($response) {
		$responseDocument = new DOMDocument();
		if ($responseDocument->loadXML($response) === false) {
			throw new RuntimeException('Could not parse rule service response.');
		}
		return $responseDocument->getElementsByTagName('Var');
	}

}
