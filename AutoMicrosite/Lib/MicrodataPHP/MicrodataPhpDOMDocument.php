<?php
namespace Lib\MicrodataPhp;

use DOMDocument;
use DOMXPath;

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
 * Extend the DOMDocument class with the Microdata API functions.
 */
class MicrodataPhpDOMDocument extends DOMDocument {
	
	public function __construct($version = '1.0', $encoding = 'UTF-8') {
		parent::__construct($version, $encoding);
		$this->registerNodeClass('DOMDocument', '\Lib\MicrodataPhp\MicrodataPhpDOMDocument');
		$this->registerNodeClass('DOMElement', '\Lib\MicrodataPhp\MicrodataPhpDOMElement');
	}

	/**
	 * Retrieves a list of microdata items.
	 *
	 * @param string $typeNames space-separated item type names
	 * @return \DOMNodeList
	 *   A DOMNodeList containing all top level microdata items.
	 */
	public function getItems($typeNames = '') {
		// TODO: make $typeNames work
		// Return top level items.
		return $this->xpath()->query('//*[@itemscope and not(@itemprop)]');
	}

	/**
	 * Creates a DOMXPath to query this document.
	 *
	 * @return
	 *   DOMXPath object.
	 */
	public function xpath() {
		return new DOMXPath($this);
	}

}

?>