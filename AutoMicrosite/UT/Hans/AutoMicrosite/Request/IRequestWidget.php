<?php
namespace UT\Hans\AutoMicrosite\Request;

/**
 * Request widget interface
 */
interface IRequestWidget {

	/**
	 * Get URL of the widget metadata URL
	 *
	 * @return string
	 */
	public function getUrl();

	/**
	 * Get the properties of the widget
	 *
	 * @return array
	 */
	public function getProperties();


}
