<?php
namespace UT\Hans\AutoMicrosite\Util;

use \ErrorException;

/**
 * cURL connection class
 *
 * @author Hans
 */
class CurlConnection {

	/**
	 * URL to connect to
	 * 
	 * @var string
	 */
	private $url;

	/**
	 * Set URL value
	 * 
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	public function __construct($url) {
		$this->url = $url;
	}

	/**
	 * Post data to URL
	 *
	 * @param string|array $data
	 * @throws \ErrorException
	 */
	public function post($data) {
		return $this->exec(array(
			CURLOPT_RETURNTRANSFER	=>	true,
			CURLOPT_POST			=>	true,
			CURLOPT_POSTFIELDS		=>	is_array($data) ? http_build_query($data) : $data
		));
	}

	/**
	 * Get URL
	 *
	 * @throws \ErrorException
	 */
	public function get() {
		return $this->exec(array(
			CURLOPT_RETURNTRANSFER	=>	true
		));
	}

	/**
	 * PUT data to URL
	 * 
	 * @param string|array $data
	 * @throws \ErrorException
	 */
	public function put($data) {
		return $this->exec(array(
			CURLOPT_RETURNTRANSFER	=>	true,
			CURLOPT_CUSTOMREQUEST	=>	'PUT',
			CURLOPT_POSTFIELDS		=>	is_array($data) ? http_build_query($data) : $data
		));
	}

	/**
	 * Execute cURL connection
	 * 
	 * @param array $options
	 * @return type
	 * @throws \ErrorException
	 */
	private function exec(array $options) {
		Log::custom('curl_exec', "REQUEST:\n". $this->url .' ('. \implode('; ', $options) .')');

		$ch = curl_init($this->url);
		foreach ($options as $optionName => $optionValue) {
			curl_setopt($ch, $optionName, $optionValue);
		}

		// TODO: find a better place for this
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));

		$return = curl_exec($ch);

		Log::custom('curl_exec', "RESPONSE:\n". $this->url ."\n". $return);

		if ($return === false) {
			throw new ErrorException('Curl exception: '. curl_error($ch));
		}

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($httpCode != 200) {
			throw new ErrorException('Curl exception: HTTP code '. $httpCode);
		}

		curl_close($ch);
		return $return;
	}

}

?>