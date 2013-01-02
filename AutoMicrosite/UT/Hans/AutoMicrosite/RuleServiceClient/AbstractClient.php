<?php
namespace UT\Hans\AutoMicrosite\RuleServiceClient;

/**
 * Abstract client class, implements constructor
 */
abstract class AbstractClient implements IClient {

	protected $url;

	protected $ruleset;

	private $templateQuery;

	private $widgetQuery;

	/**
	 * Get rule service URL
	 *
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Get ruleset to query
	 *
	 * @return string
	 */
	public function getRuleset() {
		return $this->ruleset;
	}

	/**
	 * Get template query
	 *
	 * @return string
	 */
	public function getTemplateQuery() {
		return $this->templateQuery;
	}

	/**
	 * Get widget query
	 *
	 * @param string $widget
	 * @param string $template
	 * @return string
	 */
	public function getWidgetQuery($widget, $template) {
		return \str_replace(
			array('{$widget}', '{$template}'),
			array($widget, $template),
			$this->widgetQuery);
	}

	/**
	 * @param string $url location of the RuleML service client
	 * @param string $ruleset ruleset
	 * @param string $templateQuery query for selecting template
	 * @param string $url $widgetQuery query for selecting widget information
	 * @throws \RuntimeException
	 */
	public function __construct($url, $ruleset, $templateQuery, $widgetQuery) {
		$this->url = $url;
		$this->ruleset = $ruleset;
		$this->templateQuery = $templateQuery;
		$this->widgetQuery = $widgetQuery;
	}

}
