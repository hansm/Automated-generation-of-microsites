<?php
namespace UT\Hans\AutoMicrosite\RuleServiceClient;

/**
 * Interface that RuleML service client must implement
 */
interface IClient {

	/**
	 * Get compatible template
	 *
	 * @return string
	 * @throws \RuntimeException
	 */
	public function getTemplate();

	/**
	 * Get widget information for the template
	 *
	 * @param string $widget widget identifier
	 * @param string $template template identifier
	 * @return \UT\Hans\AutoMicrosite\RuleServiceClient\IWidget
	 * @throws \RuntimeException
	 */
	public function getWidgetInfo($widget, $template);

}
