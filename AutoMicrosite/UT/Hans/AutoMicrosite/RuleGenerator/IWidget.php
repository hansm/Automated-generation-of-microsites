<?php
namespace UT\Hans\AutoMicrosite\RuleGenerator;

/**
 * Rule generateor input widget
 */
interface IWidget {

	/**
	 * Widget identifier for the rules
	 *
	 * @return string
	 */
	public function getId();

	/**
	 * Widget metadata file URL
	 *
	 * @return string
	 */
	public function getUrl();

}
