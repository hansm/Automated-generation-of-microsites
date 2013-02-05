<?php

require '../Environment.php';

use UT\Hans\AutoMicrosite\Util\CurlConnection;

/**
 * Business information
 */
class BusinessInfo {

	/**
	 * Business registry code
	 *
	 * @var int
	 */
	public $code;

	/**
	 * Business name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * @param int $code
	 * @param string $name
	 */
	public function __construct($code, $name) {
		$this->code = (int) trim($code);
		$this->name = trim($name);
	}

}

/**
 * Business information request
 */
class BusinessRegistryRequest {

	const CACHE_FILE = 'business_registry_cache';

	/**
	 * Input variables
	 *
	 * @var array
	 */
	private $input;

	/**
	 * cURL connection object
	 *
	 * @var \UT\Hans\AutoMicrosite\Util\CurlConnection
	 */
	private $connection;

	public function __construct(array $input) {
		$this->connection = new CurlConnection('https://ariregister.rik.ee/lihtparing.py');
		$this->input = $input;
		$this->process();
	}

	/**
	 * Process the request
	 */
	private function process() {
		if (empty($this->input['name'])) {
			$this->response(array(
				'error'		=>	1,
				'message'	=>	'Invalid name.'
			));
		}

		$cache = $this->getCache();
		if ($cache !== null && array_key_exists($this->input['name'], $cache)) {
			$this->response(array(
				'error'			=>	0,
				'businesses'	=>	$cache[$this->input['name']]
			));
		}

		$this->connection->setUrl(
			'https://ariregister.rik.ee/lihtparing.py?'.
			http_build_query(array(
				'lang'		=>	'eng',
				'search'	=>	1,
				'nimi'		=>	$this->input['name'],
				'sub'		=>	'Search from the Commercial Register'
			))
		);
		$this->connection->setOptions(array(
			CURLOPT_SSL_VERIFYPEER	=>	false
		));

		try {
			$response = $this->connection->get();
		} catch (ErrorException $e) {
			$this->response(array(
				'error'		=>	2,
				'message'	=>	'Could not connect to business registry.'
			));
		}

		try {
			$businessCodes = $this->findBusinessCode($response);
		} catch (Exception $e) {
			$this->response(array(
				'error'		=>	3,
				'message'	=>	'No code found.'
			));
		}

		$this->response(array(
			'error'			=>	0,
			'businesses'	=>	$businessCodes->toArray()
		));
	}

	/**
	 *
	 * @param string $pageContent
	 * @return SplFixedArray
	 * @throws Exception
	 */
	private function findBusinessCode($pageContent) {
		$match = array();
		if (!preg_match_all('#<TR valign="top">.*?<TD class="td_v"><a[^>]*>([^<]+)</a></TD>.*?<TD class="td_v">([0-9]+)</TD>.*?</TR>#is', $pageContent, $match)) {
			throw new Exception('No code found.');
		}

		$businesses = new SplFixedArray(count($match[0]));
		for ($i = 0; $i < $businesses->getSize(); $i++) {
			$businesses[$i] = new BusinessInfo($match[2][$i], $match[1][$i]);
		}

		return $businesses;
	}

	private function getCache() {
		try {
			$cacheContents = file_get_contents(self::CACHE_FILE);
			if (!$cacheContents) return null;
			return json_decode($cacheContents, true);
		} catch (Exception $e) {
			return null;
		}
	}

	private function writeCache($key, $contents) {

	}

	/**
	 * Send JSON response
	 *
	 * @param array $data
	 */
	private function response(array $data) {
		echo json_encode($data);
		exit();
	}

}

new BusinessRegistryRequest($_REQUEST);
