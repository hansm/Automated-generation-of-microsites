<?php
namespace UT\Hans\AutoMicrosite\Request;

use Exception;
use UT\Hans\AutoMicrosite\Mashup;

/**
 * Abstract request base class
 *
 * @author Hans
 */
abstract class AbstractRequest {

	/**
	 * Widget metadata files URLs
	 *
	 * @var array
	 */
	protected $widgets;

	/**
	 * Title of the mashup
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * Configuration options read from conf.ini file
	 *
	 * @var string[][]
	 */
	protected $conf;

	/**
	 * Set widget files
	 *
	 * @param array $widgets
	 */
	public function setWidgets(array $widgets) {
		$this->widgets = $widgets;
	}

	/**
	 * Get widget files
	 *
	 * @return array
	 */
	public function getWidgets() {
		return $this->widgets;
	}

	/**
	 * Get the title of the mashup
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Set the title of the mashup
	 *
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	public function __construct() {
		try {
			$this->loadConf();
			$this->setInput();
			$result = $this->buildMashup();
			$this->response($result);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	/**
	 * Set input variables
	 */
	abstract protected function setInput();

	/**
	 * Handle error response
	 *
	 * @param \Exception $e
	 */
	abstract protected function handleException(Exception $e);

	/**
	 * Send result to request
	 */
	abstract protected function response($result);

	protected function buildMashup() {
		$mashup = new Mashup($this->getConf());
		$mashup->setTitle(\htmlentities($this->getTitle()));
		$mashup->addWidgets($this->getWidgets());
		$mashup->applyRules();
		$mashup->output();
	}

	/**
	 * Get configuration value
	 *
	 * @param string $sectionName
	 * @param string $confName
	 * @return array|string|NULL
	 */
	public function getConf($sectionName = null, $confName = null) {
		if (isset($sectionName) && isset($confName)) {
			return isset($this->conf[$sectionName][$confName]) ? $this->conf[$sectionName][$confName] : null;
		} elseif (isset($sectionName)) {
			return isset($this->conf[$sectionName]) ? $this->conf[$sectionName] : null;
		}
		return $this->conf;
	}

	/**
	 * Load configuration file
	 *
	 * @throws Exception
	 */
	private function loadConf() {
		$conf = \parse_ini_file('conf.ini', true);
		if (!$conf) {
			throw new Exception('Could not read configuration file.');
		}
		$this->conf = $conf;
	}

	protected function saveToFile() {
		$this->getConf('general', 'mashup_dir');
	}

}
