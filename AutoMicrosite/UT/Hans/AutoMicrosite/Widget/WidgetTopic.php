<?php
namespace UT\Hans\AutoMicrosite\Widget;

/**
 * Widget topic
 *
 * @author Hans
 */
class WidgetTopic {

	/**
	 * Name of the topic
	 * @var string
	 */
	private $name;

	/**
	 * Is subscribed to topic
	 * @var boolean
	 */
	private $subscribe;

	/**
	 * Is publishing to topic
	 * @var boolean
	 */
	private $publish;

	/**
	 * Widget topci name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Whether widget subscribes to this topic
	 * @return boolean
	 */
	public function isSubscirbing() {
		return $this->subscribe;
	}

	/**
	 * Whether widget is publishing to this topic
	 * @return type
	 */
	public function isPublishing() {
		return $this->publish;
	}

	public function __construct($name, $subscribe, $publish) {
		$this->name = $name;
		$this->subscribe = $subscribe === 'true' || $subscribe === true;
		$this->publish = $publish === 'true' || $publish === true;
	}

}

?>