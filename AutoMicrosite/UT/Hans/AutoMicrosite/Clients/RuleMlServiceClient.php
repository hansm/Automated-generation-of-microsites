<?php
namespace UT\Hans\AutoMicrosite\Clients;

use \UT\Hans\AutoMicrosite\Util\CurlConnection;
use \UT\Hans\AutoMicrosite\RuleMl\RuleMl;
use \ErrorException;

/**
 * RuleML service client code
 *
 * @author Hans
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

	public function __construct($serviceUrl) {
		$this->serviceUrl = $serviceUrl;
		$this->curlConnection = new CurlConnection($this->serviceUrl);
	}

	/**
	 * Create new ruleset at RuleML service
	 *
	 * @param \UT\Hans\AutoMicrosite\RuleMl\RuleMl $ruleMl
	 * @return int
	 * @throws \ErrorException
	 */
	public function create(RuleMl $ruleMl) {
		$this->curlConnection->setUrl($this->serviceUrl);
		$responseData = $this->curlConnection->post(json_encode(array(
			'ruleset'	=>	$ruleMl->getString()
		)));
		$responseObject = \json_decode($responseData);
		return (int) $responseObject->id;
	}

	/**
	 * Append rules to ruleset at RuleML service
	 *
	 * @param int $id ID of the ruleset at service
	 * @param RuleMl $ruleMl new rules to append to ruleset
	 * @return int
	 * @throws \ErrorException
	 */
	public function append($id, RuleMl $ruleMl) {
		$this->curlConnection->setUrl($this->serviceUrl .'/append/'. $id);
		$responseData = $this->curlConnection->put(json_encode(array(
			'ruleset'	=>	$ruleMl->getString()
		)));
		$responseObject = \json_decode($responseData);
		return (int) $responseObject->id;
	}

	/**
	 * Get ruleset stored at RuleML service
	 *
	 * @param int $id
	 * @return \UT\Hans\AutoMicrosite\RuleMl\RuleMl
	 * @throws \ErrorException
	 */
	public function get($id) {
		$this->curlConnection->setUrl($this->serviceUrl .'/'. $id);
		$responseData = $this->curlConnection->get();
		$responseObject = \json_decode($responseData);
		return RuleMl::createFromString($responseObject->ruleset);
	}

	/**
	 * Query ruleset
	 *
	 * @param int $id ruleset ID
	 * @param \UT\Hans\AutoMicrosite\RuleMl\RuleMl $query
	 */
	public function query($id, RuleMl $query) {
		$this->curlConnection->setUrl($this->serviceUrl .'/query/'. $id);
		$responseData = $this->curlConnection->post(json_encode(array(
			'query'	=>	$query->getString()
		)));
		$responseObject = \json_decode($responseData);
		return RuleMl::createFromString($responseObject->ruleset);
	}

}

?>