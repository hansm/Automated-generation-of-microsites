<?php
namespace UT\Hans\AutoMicrosite\MappingsGenerator;

use Exception;
use RuntimeException;
use DOMDocument;
use DOMNode;
use DOMElement;

/**
 * Generate mappings from OpenAjax metadata file
 */
class OpenAjaxMetadataMappingsGenerator implements IMappingsGenerator {

	/**
	 * OpenAjax metadata XML namespace
	 */
	const OAMETADATA_NS = 'http://openajax.org/metadata';

	public function getMappings($metadataFileUrl) {
		$mappingsDocument = new DOMDocument();
		$metaDocument = new DOMDocument();

		try {
			$metadataContents = \file_get_contents($metadataFileUrl);
			if (empty($metadataContents)) {
				throw new Exception('Could not load metadata file.');
			}


			if (!$metaDocument->loadXML($metadataContents)) {
				throw new Exception('Invalid XML.');
			}
		} catch (Exception $e) {
			throw new RuntimeException('Could not load metadata file.');
		}

		$framesElement = $mappingsDocument->appendChild(
			$mappingsDocument->createElement('frames')
		);

		$topics = $metaDocument->getElementsByTagNameNS(self::OAMETADATA_NS, 'topic');
		if ($topics->length == 0) {
			return '';
		}

		for ($i = 0; $i < $topics->length; $i++) {
			$this->processTopic($topics->item($i), $framesElement);
		}

		$result = $mappingsDocument->saveXML($framesElement);
		if ($result === false) {
			throw new RuntimeException('Could not generate mappings XML.');
		}

		return $result;
	}

	/**
	 * Process topic node
	 *
	 * @param \DOMNode $topic
	 * @param \DOMElement $frames
	 * @throws \RuntimeException
	 */
	private function processTopic(DOMNode $topic, DOMElement $frames) {
		$document = $frames->ownerDocument;

		$frame = $frames->appendChild($document->createElement('frame'));

		// Name of the topic
		if (!($topicName = $topic->attributes->getNamedItem('name'))) {
			throw new RuntimeException('Topic name not found.');
		}

		$frameTopic = $frame->appendChild($document->createElement('topic', $topicName->nodeValue));

		// Outgoing topic only
		if (!$topic->attributes->getNamedItem('subscribe')
				|| \strcasecmp($topic->attributes->getNamedItem('subscribe')->nodeValue, 'true') != 0) {
			$outgoingOnly = $document->createAttribute('outgoing_only');
			$outgoingOnly->value = 'true';
			$frameTopic->appendChild($outgoingOnly);
		}

		// Format
		$frame->appendChild($document->createElement('format',
			($this->getDataType($topic) == 'string' ? 'string' : 'json')
		));

		// Mappings
		$mappings = $frame->appendChild($document->createElement('mappings'));

		$jsonSchema = $this->processProperty($topic, $mappings);

		$frame->appendChild($document->createElement('schema_data',
			\json_encode($jsonSchema)
		));
	}

	/**
	 * Recursively process properties into mappings
	 *
	 * @param \DOMNode $property
	 * @param \DOMElement $mappings
	 * @param string $path
	 * @return array
	 * @throws \RuntimeException
	 */
	private function processProperty(DOMNode $property, DOMElement $mappings, $path = '') {
		$type = $this->getDataType($property);
		$jsonSchema = array(
			'type'	=>	$type
		);

		if ($type == 'object') {
			$jsonSchema['properties'] = array();
			for ($i = 0; $i < $property->childNodes->length; $i++) {
				$childProperty = $property->childNodes->item($i);
				if ($childProperty->localName != 'property') {
					continue;
				}
				$childName = $this->getName($childProperty);
				if (empty($childName)) {
					throw new RuntimeException('Property name not found.');
				}
				$jsonSchema['properties'][$childName] =
					$this->processProperty($childProperty, $mappings, $path .'/'. $childName);
			}
		} elseif ($type == 'array') {
			for ($i = 0; $i < $property->childNodes->length; $i++) {
				$childProperty = $property->childNodes->item($i);
				if ($childProperty->localName != 'property') {
					continue;
				}

				// Create <repeating_element_group> for the array
				$document = $mappings->ownerDocument;
				$elementGroup = $mappings->appendChild(
					$document->createElement('repeating_element_group')
				);
				$pathAttr = $document->createAttribute('path');
				$pathAttr->value = empty($path) ? '/' : $path;
				$elementGroup->appendChild($pathAttr);

				$jsonSchema['items'] =
					$this->processProperty($childProperty, $elementGroup, $path);
				break;
			}
		} else {
			$urlParam = $this->getUrlParam($property);
			if (!empty($urlParam)) {
				// Create mapping
				$document = $mappings->ownerDocument;
				$mapping = $mappings->appendChild($document->createElement('mapping'));
				$mapping->appendChild($document->createElement('global_ref', $urlParam));
				$mapping->appendChild($document->createElement('path', empty($path) ? '/' : $path));
			}
		}

		return $jsonSchema;
	}

	/**
	 * Get name of the property
	 *
	 * @param DOMNode $property
	 * @return string|null
	 */
	private function getName(DOMNode $property) {
		$nameAttr = $property->attributes->getNamedItem('name');
		if (!$nameAttr || empty($nameAttr->nodeValue)) {
			return null;
		}
		return $nameAttr->nodeValue;
	}

	/**
	 * Get data type of property
	 *
	 * @param \DOMNode $property
	 * @return string
	 */
	private function getDataType(DOMNode $property) {
		$typeAttr = $property->attributes->getNamedItem('datatype');
		if (!$typeAttr) {
			$typeAttr = $property->attributes->getNamedItem('type');
		}
		if ($typeAttr) {
			if (\strcasecmp($typeAttr->nodeValue, 'string') == 0) {
				return 'string';
			} elseif (\strcasecmp($typeAttr->nodeValue, 'number') == 0) {
				return 'number';
			} elseif (\strcasecmp($typeAttr->nodeValue, 'boolean') == 0) {
				return 'boolean';
			} elseif (\strcasecmp($typeAttr->nodeValue, 'array') == 0) {
				return 'array';
			} elseif (\strcasecmp($typeAttr->nodeValue, 'object') == 0) {
				return 'object';
			} elseif (\strcasecmp($typeAttr->nodeValue, 'null') == 0) {
				return 'null';
			}
		}
		if ($property->hasChildNodes()) {
			return 'object';
		}
		return 'string';
	}

	/**
	 *
	 *
	 * @param \DOMNode $property
	 * @return string|null
	 */
	private function getUrlParam(DOMNode $property) {
		$urlParamAttr = $property->attributes->getNamedItem('urlparam');
		if (!$urlParamAttr || empty($urlParamAttr->nodeValue)) {
			return null;
		}
		return $urlParamAttr->nodeValue;
	}

}
