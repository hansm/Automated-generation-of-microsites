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
	 * @return \UT\Hans\AutoMicrosite\Request\IRequest
	 */
	public static function build($requestType) {
		switch (\strtoupper($requestType)) {
			case 'GET':
				return new GET();
			case 'JSON':
				return new JSON();
		}
		echo 'Invalid request type.';
		exit();
	}

}
