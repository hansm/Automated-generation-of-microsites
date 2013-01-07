<?php
namespace UT\Hans\AutoMicrosite\RuleGenerator;

/**
 * Rule generator input template
 */
interface ITemplate {

	/**
	 * Get template identifier
	 *
	 * @return string
	 */
	public function getId();

	/**
	 * Get template file URL
	 *
	 * @return string
	 */
	public function getUrl();

}
