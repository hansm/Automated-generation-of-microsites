<?php
namespace UT\Hans\AutoMicrosite\Request;

use Exception;

/**
 * Request interface
 *
 * @author Hans
 */
interface IRequest {

	/**
	 * Get widget files
	 *
	 * @return array
	 */
	public function getWidgets();

	/**
	 * Get the title of the mashup
	 *
	 * @return string
	 */
	public function getTitle();

	/**
	 * Set input variables
	 */
	public function setInput();

	/**
	 * Handle error response
	 * 
	 * @param \Exception $e
	 */
	public function handleException(Exception $e);

	/**
	 * Send result to request
	 * 
	 * @param string $result
	 */
	public function response($result);
	
}
