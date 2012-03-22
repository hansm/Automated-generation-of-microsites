<?php
namespace UT\Hans\AutoMicrosite;

use \DOMDocument;
use \DOMNodeList;
use \DOMNode;
use \SplFixedArray;

/**
 * Widget creationg class
 */
class Widget {

	/**
	 * Metadata file location
	 * @var string
	 */
	private $metadataFile;

	/**
	 * Widget categories array
	 * @var \SplFixedArray
	 */
	private $categories;

	/**
	 * Widget topics
	 * @var \SplFixedArray
	 */
	private $topics;

	/**
	 * Widget preferred width
	 * @var int
	 */
	private $width;

	/**
	 * Widget preferred height
	 * @var type
	 */
	private $height;

	/**
	 * Widget priority, higher priority widgets should appear before lower priority widgets
	 * @var int
	 */
	private $priority;

	private $position;

	/**
	 * Widget getter
	 * @return \SplFixedArray
	 */
	public function getCategories() {
		return $this->categories;
	}

	public function getPriority() {
		return $this->priority;
	}

	public function setPriority($priority) {
		$this->priority = $priority;
	}
	
	public function getPosition() {
		return $this->position;
	}

	public function setPosition($position) {
		$this->position = $position;
	}

	public function __construct($file) {
		$this->metadataFile = realpath($file);
		if ($this->metadataFile === false) {
			throw new WidgetException('Metadata file not found.');
		}
	}

	/**
	 * Load necessary information about widget from metadata file
	 * @throws WidgetException
	 * @throws DOMException
	 */
	public function loadWidgetData() {
		$dom = new DOMDocument();
		$dom->load($this->metadataFile);
		if ($dom === false) {
			throw new WidgetException('Failed to load widget metadata.');
		}

		// load categories
		$categoryNodes = $dom->getElementsByTagNameNS('http://openajax.org/metadata', 'category');
		$this->categories = new SplFixedArray($categoryNodes->length);
		for ($i = 0; $i < $categoryNodes->length; $i++) {
			$categoryNameAttribute = $categoryNodes->item($i)->attributes->getNamedItem('name');
			if ($categoryNameAttribute === null) {
				throw new WidgetException('Invalid widget category.');
			}
			$this->categories[$i] = $categoryNameAttribute->nodeValue;
		}

		// load topics
		$topicNodes = $dom->getElementsByTagNameNS('http://openajax.org/metadata', 'topic');
		$this->topics = new SplFixedArray($topicNodes->length);
		for ($i = 0; $i < $topicNodes->length; $i++) {
			$topicNode = $topicNodes->item($i);
			$topicNameAttribute = $topicNode->attributes->getNamedItem('name');
			$topicSubscribeAttribute = $topicNode->attributes->getNamedItem('subscribe');
			$topicPublishAttribute = $topicNode->attributes->getNamedItem('publish');
			if ($topicNameAttribute === null) {
				throw new WidgetException('Invalid widget topic.');
			}
			$this->topics[$i] = new WidgetTopic($topicNameAttribute->nodeValue
				, ($topicSubscribeAttribute !== null ? $topicSubscribeAttribute->nodeValue : false)
				, ($topicPublishAttribute !== null ? $topicPublishAttribute->nodeValue : false));
		}

		// TODO: priority
		// TODO: some kind of connections between widgets
	}

	/**
	 * Get metadata file URL
	 * @return string
	 */
	public function getUrl() {
		// TODO: it should do some more magic to work in more general cases
		$file = str_replace('\\', '/', $this->metadataFile);
		return 'widgets/'. end(explode('/', $file));
	}

	/**
	 * Return widget in JSON format for inclusion in hub
	 * @return string
	 */
	public function toJson() {
		// TODO: add new stuff
		$data = array(
			'file'		=>	$this->getUrl(),
			'priority'	=>	$this->priority,
			'position'	=>	$this->position,

			// TODO: maybe not needed, these are loaded from widget file anyways
			'width'		=>	$this->width,
			'height'	=>	$this->height
		);
		return json_encode($data);
	}

}

?>