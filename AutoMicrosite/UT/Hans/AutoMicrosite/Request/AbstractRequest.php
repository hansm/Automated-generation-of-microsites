<?php
namespace UT\Hans\AutoMicrosite\Request;

use Exception;
use ErrorException;
use UT\Hans\AutoMicrosite\Mashup;
use UT\Hans\AutoMicrosite\Util\Log;

/**
 * Abstract request base class
 *
 * @author Hans
 */
abstract class AbstractRequest implements IRequest {

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
	 * Log writter
	 *
	 * @var \UT\Hans\AutoMicrosite\Util\Log
	 */
	protected $log;

	/**
	 * Set widget files
	 *
	 * @param array $widgets
	 */
	public function setWidgets(array $widgets) {
		$this->widgets = $widgets;
	}

	public function getWidgets() {
		return $this->widgets;
	}

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
		$this->log = new Log('request_handler');
		try {
			$this->loadConf();
			$this->setInput();
			if (!$result = $this->getCache()) {
				$result = $this->buildMashup();
				$this->saveCache($result);
			}
			$this->response($result);
		} catch (Exception $e) {
			$this->log->exception($e);
			$this->handleException($e);
		}
	}

	/**
	 * Construct mashup
	 *
	 * @throws \RuntimeException
	 */
/*	protected function buildMashup() {
		$mashup = new Mashup($this->getConf());
		$mashup->setTitle(\htmlentities($this->getTitle()));
		$mashup->addWidgets($this->getWidgets());
		$mashup->applyRules();
		$mashup->output();
	}*/
	protected function buildMashup() {
		$mashup = new Mashup($this->getConf());
		return $mashup->process($this->getTitle(), $this->getWidgets());
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

	/**
	 * Try to load cached mashup
	 *
	 * @return string
	 */
	protected function getCache() {
		if (!$this->getConf('general', 'cache')
				|| !$this->getConf('general', 'cache_dir')) return '';

		$cacheFileName = self::calcHash($this->getTitle(), $this->getWidgets()) .'.html';
		$cacheFileFullName = $this->getConf('general', 'cache_dir') . $cacheFileName;

		if (!\file_exists($cacheFileFullName)) {
			return '';
		}

		$cacheCreateDate = \filemtime($cacheFileFullName);
		foreach ($this->getConf('rules') as $rulesFile) {
			if (\filemtime($rulesFile) > $cacheCreateDate) {
				return '';
			}
		}

		try {
			return \file_get_contents($cacheFileFullName);;
		} catch (ErrorException $e) {
			$this->log->exception($e);
			return '';
		}
	}

	/**
	 * Save cache to disk
	 *
	 * @param string $content
	 */
	protected function saveCache($content) {
		if (!$this->getConf('general', 'cache')
				|| !$this->getConf('general', 'cache_dir')) return;

		$cacheFileName = self::calcHash($this->getTitle(), $this->getWidgets()) .'.html';
		try {
			\file_put_contents($this->getConf('general', 'cache_dir') . $cacheFileName,
				$content);
		} catch (ErrorException $e) {
			$this->log->exception($e);
		}
	}

	/**
	 * Calculate a "unique" hash for the input
	 *
	 * @param type $title
	 * @param \UT\Hans\AutoMicrosite\Request\IRequestWidget[] $widgets
	 */
	public static function calcHash($title, $widgets) {
		usort($widgets, function($a, $b) {
			return strcasecmp($a->getUrl(), $b->getUrl());
		});

		$hashString = $title .':';
		foreach ($widgets as $widget) {
			$hashString .= $widget->getUrl() .';';
			foreach ($widget->getProperties() as $propName => $propValue) {
				$hashString .= $propName .'='. $propValue .';';
			}
			$hashString .= $widget->getFlowOrder() .';';
		}

		return \sha1($hashString);
	}

}