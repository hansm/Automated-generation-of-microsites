<?php
namespace UT\Hans\AutoMicrosite\RuleServiceClient;

/**
 * Widget info interface returned by service client
 */
interface IWidget {

	/**
	 * Get identifier of the widget
	 *
	 * @return string
	 */
	public function getId();

	/**
	 * Get the identifier of the placeholder where widget belongs
	 *
	 * @return string|null
	 */
	public function getPlaceholder();

	/**
	 * Get the priority of the widget
	 *
	 * @return string|null
	 */
	public function getPriority();

	/**
	 * Whether the widget is data widget
	 *
	 * @return boolean
	 */
	public function isDataWidget();

	/**
	 * Whether the widget needs a separate page
	 *
	 * @return boolean
	 */
	public function separatePage();

	/**
	 * Line number of the widget in a placeholder
	 *
	 * @return int
	 */
	public function getLineNumber();
	
	public function getMaxWidth();

	public function getMinWidth();

	public function getMinHeight();

	public function getMaxHeight();

}
