<?php
namespace UT\Hans\AutoMicrosite\RuleServiceClient;

use RuntimeException;

/**
 * Build request object
 */
class Factory {

	/**
	 * @param string $requestType
	 * @return \UT\Hans\AutoMicrosite\RuleServiceClient\IClient
	 * @throws \RuntimeException
	 */
	public static function build($serviceType, $serviceDomain, $ruleset, $templateQuery, $widgetQuery) {
		switch (\strtoupper($serviceType)) {
			case 'OOJDREW':
				return new RuleMlService($serviceDomain, $ruleset, $templateQuery, $widgetQuery);
		}
		throw new RuntimeException('Rule service not implemented.');
	}

}
