<?php
namespace UT\Hans\AutoMicrosite\Util;

use ErrorException;

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
	 * Options to always add to the request
	 *
	 * @var array
	 */
	private $options = array();

	/**
	 * Log handler
	 *
	 * @var \UT\Hans\AutoMicrosite\Util\Log
	 */
	private $log;

	/**
	 * Set URL value
	 *
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * Set cURL connection options
	 *
	 * @param array $options
	 */
	public function setOptions(array $options) {
		$this->options = $options;
	}

	/**
	 * @param string $url
	 * @param \UT\Hans\AutoMicrosite\Util\Log $log
	 */
	public function __construct($url, Log $log = null) {
		$this->url = $url;
		$this->log = empty($log) ? new Log('curl_exec') : $log;
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
			CURLOPT_POSTFIELDS		=>	\is_array($data) ? \http_build_query($data) : $data
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
			CURLOPT_POSTFIELDS		=>	\is_array($data) ? \http_build_query($data) : $data
		));
	}

	/**
	 * Execute cURL connection
	 *
	 * @param array $options
	 * @return string
	 * @throws \ErrorException
	 */
	private function exec(array $options) {
		$options += $this->options;

		if (isset($options[CURLOPT_CUSTOMREQUEST])) {
			$action = $options[CURLOPT_CUSTOMREQUEST];
		} elseif (isset($options[CURLOPT_POST]) && $options[CURLOPT_POST]) {
			$action = 'POST';
		} else {
			$action = 'GET';
		}
		$this->log->info("REQUEST:\n"
			. $action . ' '
			. $this->url . "\n"
			. (isset($options[CURLOPT_HTTPHEADER]) ? implode("\n", $options[CURLOPT_HTTPHEADER]) . "\n\n" : '')
			. (isset($options[CURLOPT_POSTFIELDS]) ? $options[CURLOPT_POSTFIELDS] : '')
			. "\n");

		// Data is in UTF-8 encoding and JSON
		if (!isset($options[CURLOPT_HTTPHEADER])) {
			$options[CURLOPT_HTTPHEADER] = array('Content-Type: application/json; charset=utf-8');
		}

		$ch = \curl_init($this->url);
		foreach ($options as $optionName => $optionValue) {
			\curl_setopt($ch, $optionName, $optionValue);
		}

		$return = \curl_exec($ch);
		$this->log->info("RESPONSE:\n". $this->url ."\n". $return . "\n\n");

		if ($return === false) {
			throw new ErrorException('Curl exception: '. \curl_error($ch));
		}

		$httpCode = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($httpCode != 200) {
			throw new ErrorException('Curl exception: HTTP code '. $httpCode);
		}

		\curl_close($ch);

		return $return;
	}

}
