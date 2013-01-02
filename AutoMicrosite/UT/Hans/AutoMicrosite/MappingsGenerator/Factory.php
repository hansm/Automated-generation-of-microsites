<?php
namespace UT\Hans\AutoMicrosite\MappingsGenerator;

/**
 * Build mappings generator object
 */
class Factory {

	/**
	 * @return \UT\Hans\AutoMicrosite\MappingsGenerator\IMappingsGenerator
	 */
	public static function build($metadataType) {
		switch (\strtoupper($metadataType)) {
			case 'OPENAJAXMETADATA':
				return new OpenAjaxMetadataMappingsGenerator();
		}
		throw new \RuntimeException('RuleML service not implemented.');
	}

}
