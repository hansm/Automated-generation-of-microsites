<?php
namespace UT\Hans\AutoMicrosite\MappingsGenerator;

/**
 * Interface for widget messages mappings generator
 */
interface IMappingsGenerator {

	/**
	 * Generate mappings from widget Metadata file
	 *
	 * @param string $metadataFileUrl
	 * @return string
	 * @throws RuntimeException
	 */
	public function getMappings($metadataFileUrl);

}
