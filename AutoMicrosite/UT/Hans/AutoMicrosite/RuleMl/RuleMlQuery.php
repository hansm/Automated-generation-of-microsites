<?php
namespace UT\Hans\AutoMicrosite\RuleMl;

/**
 * Description of RuleMlQuery
 *
 * @author Hans
 */
class RuleMlQuery extends RuleMl {

	/**
	 * Create RuleML query
	 *
	 * @param int $widgetId
	 * @return \UT\Hans\AutoMicrosite\RuleMl\RuleMlQuery
	 */
	public static function createQuery($widgetId) {
		$ruleMl = new RuleMlQuery();
		$queryString = \file_get_contents('Rules/Query.ruleml');
		$queryString = \str_replace('{$widget}', $widgetId, $queryString);
		$ruleMl->setFromString($queryString);
		return $ruleMl;
	}

}

?>
