<?php
namespace UT\Hans\AutoMicrosite\Request;

/**
 * Build request object
 *
 * @author Hans
 */
class Factory {

	/**
	 * @param string $requestType
	 * @return \UT\Hans\AutoMicrosite\Request\AbstractRequest
	 */
	public static function build($requestType) {
		switch (\strtoupper($requestType)) {
			case 'GET':
				return new GET();
			case 'REST':
				return new REST();
		}
		echo 'Invalid request type.';
		exit();
	}

}
