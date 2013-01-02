<?php
namespace UT\Hans\AutoMicrosite\RuleServiceClient;

use UT\Hans\AutoMicrosite\Util\CurlConnection;
use UT\Hans\AutoMicrosite\RuleMl\RuleMl;

/**
 * RuleML service client code
 */
class RuleMlServiceClient {

	/**
	 * RuleML service URL
	 *
	 * @var string
	 */
	private $serviceUrl;

	/**
	 * Curl connection class
	 *
	 * @var \UT\Hans\AutoMicrosite\Util\CurlConnection
	 */
	private $curlConnection;

	/**
	 * @param string $serviceUrl
	 */
	public function __construct($serviceUrl) {
		$this->serviceUrl = $serviceUrl;
		$this->curlConnection = new CurlConnection($this->serviceUrl);
	}

	/**
	 * Create new ruleset at RuleML service
	 *
	 * @param string $ruleset
	 * @return int
	 * @throws \ErrorException
	 */
	public function create($ruleset) {
		$this->curlConnection->setUrl($this->serviceUrl);
		$responseData = $this->curlConnection->post(json_encode(array(
			'ruleset'	=>	$ruleset
		)));
		$responseObject = \json_decode($responseData);
		return (int) $responseObject->id;
	}

	/**
	 * Append rules to ruleset at RuleML service
	 *
	 * @param int $id ID of the ruleset at service
	 * @param string $ruleset new rules to append to ruleset
	 * @return int
	 * @throws \ErrorException
	 */
	public function append($id, $ruleset) {
		$this->curlConnection->setUrl($this->serviceUrl .'/append/'. $id);
		$responseData = $this->curlConnection->put(json_encode(array(
			'ruleset'	=>	$ruleset
		)));
		$responseObject = \json_decode($responseData);
		return (int) $responseObject->id;
	}

	/**
	 * Get ruleset stored at RuleML service
	 *
	 * @param int $id
	 * @return string
	 * @throws \ErrorException
	 */
	public function get($id) {
		$this->curlConnection->setUrl($this->serviceUrl .'/'. $id);
		$responseData = $this->curlConnection->get();
		$responseObject = \json_decode($responseData);
		return $responseObject->ruleset;
	}

	/**
	 * Query ruleset
	 *
	 * @param int $id ruleset ID
	 * @param string $query
	 * @return string
	 */
	public function query($id, $query) {
		$this->curlConnection->setUrl($this->serviceUrl .'/query/'. $id);
		$responseData = $this->curlConnection->post(json_encode(array(
			'query'	=>	$query
		)));
		$responseObject = \json_decode($responseData);
		return $responseObject->ruleset;
	}

}
