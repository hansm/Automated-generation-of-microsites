<?php
namespace UT\Hans\AutoMicrosite\Template;

use DOMDocument;

/**
 * DOM HTML functions
 *
 * @author Hans
 */
class DomHtml {
	
	/**
	 * Convert string into DOMElement so it can be used in DOMDocument
	 * 
	 * @param string $html
	 * @param \DOMDocument $document 
	 */
	public static function stringToDomElement($html, DOMDocument $document) {
		$newDocument = new DOMDocument('1.0', 'UTF-8');
		$newDocument->loadHTML('<html><body><div>'. $html .'</div></body></html>');
		$newElement = $newDocument->getElementsByTagName('body')->item(0)->firstChild;
		return $document->importNode($newElement, true);
	}
	
}

?>