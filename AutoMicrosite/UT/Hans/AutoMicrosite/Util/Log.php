<?php
namespace UT\Hans\AutoMicrosite\Util;

/**
 * Description of Log
 *
 * @author Hans
 */
class Log {

	/**
	 * Write custom log to file
	 * 
	 * @param string $logFile file for log
	 * @param string $logText text to log
	 */
	public static function custom($logFile, $logText) {
		file_put_contents('log/'. $logFile, date('r') ." - ". $logText ."\n\n", FILE_APPEND);
	}

}

?>