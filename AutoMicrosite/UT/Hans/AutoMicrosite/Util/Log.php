<?php
namespace UT\Hans\AutoMicrosite\Util;

use Exception;

/**
 * Log error messages
 *
 * @author Hans
 */
class Log {

	/**
	 * Log file name
	 *
	 * @var string
	 */
	private $file;

	/**
	 * @param string $logFile
	 */
	public function __construct($logFile) {
		$this->file = $logFile;
	}

	/**
	 * Log exception
	 *
	 * @param Exception $e
	 */
	public function exception(Exception $e) {
		$this->write(get_class($e) ."\t"
			. $e->getCode() ."\t"
			. $e->getMessage() ."\t"
			. \str_replace("\n", " ", $e->getTraceAsString()) );
	}

	/**
	 * Log general info
	 *
	 * @param string $logText
	 */
	public function info($logText) {
		$this->write($logText);
	}

	/**
	 * Write log into file
	 *
	 * @param string $logText
	 */
	private function write($logText) {
		try {
			\file_put_contents('log/'. $this->file
				, \date('r') . "\t" . $logText ."\n\n"
				, FILE_APPEND);
		} catch (Exception $e) {}
	}

	/**
	 * Write custom log to file
	 *
	 * @param string $logFile file for log
	 * @param string $logText text to log
	 */
	public static function custom($logFile, $logText) {
		$log = new self();
		$log->info($logText);
	}

}
