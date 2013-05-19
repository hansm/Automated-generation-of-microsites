<?php
namespace UT\Hans\AutoMicrosite\RuleServiceClient;

/**
 * Parse RuleML service response into an object
 */
class RuleMlWidget implements IWidget {

	/**
	 * Widget data received from rule service
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Response variables cast to integers
	 *
	 * @var string[]
	 */
	private $intVars = array('priority', 'line');

	/**
	 * Response variables converted to boolean
	 *
	 * @var string[]
	 */
	private $booleanVars = array('isDataWidget', 'separatePage', 'isMenuWidget', 'loadFirst');

	public function __construct($response) {
		$variables = RuleMlService::getResponseVarNodes($response);
		for ($i = 0; $i < $variables->length; $i++) {
			$var = $variables->item($i);
			$varName = RuleMlService::getResponseVarName($var);
			$varValue = RuleMlService::getResponseVarValue($var);
			if ($varValue === 'NULL') { // no value
				$this->data[$varName] = null;
			} elseif (\in_array($varName, $this->intVars)) {
				$this->data[$varName] = (int) $varValue;
			} elseif (\in_array($varName, $this->booleanVars)) {
				$this->data[$varName] = strcasecmp($varValue, 'true') == 0;
			} else {
				$this->data[$varName] = $varValue;
			}
		}
	}

	/**
	 * Get response variable value
	 *
	 * @param string $key
	 * @return string|int|boolean|null
	 */
	private function getValue($key) {
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}

	public function getId() {
		return $this->getValue('widget');
	}

	public function getPlaceholder() {
		return $this->getValue('placeholder');
	}

	public function getPriority() {
		return $this->getValue('priority');
	}

	public function isDataWidget() {
		return $this->getValue('isDataWidget');
	}

	public function isMenuWidget() {
		return $this->getValue('isMenuWidget');
	}

	public function separatePage() {
		return $this->getValue('separatePage');
	}

	public function getMaxHeight() {

	}

	public function getMaxWidth() {

	}

	public function getMinHeight() {

	}

	public function getMinWidth() {

	}

	public function getLoadFirst() {
		return $this->getValue('loadFirst');
	}

}
