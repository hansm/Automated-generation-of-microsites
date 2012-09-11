<?php
namespace Lib\MicrodataPhp;

/**
 * MicrodataPHP
 * http://github.com/linclark/MicrodataPHP
 * Copyright (c) 2011 Lin Clark
 * Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 *
 * Based on MicrodataJS
 * http://gitorious.org/microdatajs/microdatajs
 * Copyright (c) 2009-2011 Philip Jägenstedt
 */
/**
 * Extracts microdata from HTML.
 *
 * Currently supported formats:
 *   - PHP object
 *   - JSON
 */
class MicrodataPhp {

	public $dom;

	/**
	 * Constructs a MicrodataPhp object.
	 *
	 * @param $url
	 *   The url of the page to be parsed.
	 * @throws \RuntimeException
	 */
	public function __construct($url) {
		$dom = new MicrodataPhpDOMDocument($url);
		$dom->preserveWhiteSpace = false;
		if (!@$dom->loadHTMLFile($url)) {
			throw new \RuntimeException('Could not load HTML file.');
		}
		$this->dom = $dom;
	}

	/**
	 * Retrieve microdata as a PHP object.
	 *
	 * @return
	 *   An object with an 'items' property, which is an array of top level
	 *   microdata items as objects with the following properties:
	 *   - type: An array of itemtype(s) for the item, if specified.
	 *   - id: The itemid of the item, if specified.
	 *   - properties: An array of itemprops. Each itemprop is keyed by the
	 *     itemprop name and has its own array of values. Values can be strings
	 *     or can be other items, represented as objects.
	 *
	 * @todo MicrodataJS allows callers to pass in a selector for limiting the
	 *   parsing to one section of the document. Consider adding such
	 *   functionality.
	 */
	public function obj() {
		$result = new stdClass();
		$result->items = array();
		foreach ($this->dom->getItems() as $item) {
			array_push($result->items, $this->getObject($item, array()));
		}
		return $result;
	}

	/**
	 * Retrieve microdata in JSON format.
	 *
	 * @return
	 *   See obj().
	 *
	 * @todo MicrodataJS allows callers to pass in a function to format the JSON.
	 * Consider adding such functionality.
	 */
	public function json() {
		return json_encode($this->obj());
	}

	/**
	 * Helper function.
	 *
	 * In MicrodataJS, this is handled using a closure. PHP 5.3 allows closures,
	 * but cannot use $this within the closure. PHP 5.4 reintroduces support for
	 * $this. When PHP 5.3/5.4 are more widely supported on shared hosting,
	 * this function could be handled with a closure.
	 */
	protected function getObject($item, $memory) {
		$result = new stdClass();
		$result->properties = array();

		// Add itemtype.
		if ($itemtype = $item->itemType()) {
			$result->type = $itemtype;
		}
		// Add itemid. 
		if ($itemid = $item->itemid()) {
			$result->id = $itemid;
		}
		// Add properties.
		foreach ($item->properties() as $elem) {
			if ($elem->itemScope()) {
				if (in_array($elem, $memory)) {
					$value = 'ERROR';
				} else {
					$memory[] = $item;
					$value = $this->getObject($elem, $memory);
					array_pop($memory);
				}
			} else {
				$value = $elem->itemValue();
			}
			foreach ($elem->itemProp() as $prop) {
				$result->properties[$prop][] = $value;
			}
		}

		return $result;
	}

}

?>