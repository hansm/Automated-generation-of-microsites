<?php
namespace UT\Hans\AutoMicrosite\Template;

use UT\Hans\AutoMicrosite\RuleMl\RuleMl;

/**
 * Description of Templates
 *
 * @author Hans
 */
class Templates {
	
	const TEMPLATES_DIR = 'Templates';
	
	private $templates = array();
	
	public function __construct() {
		$templatesDir = \dir(ROOT . self::TEMPLATES_DIR . DIRECTORY_SEPARATOR);
		if (!$templatesDir) {
			throw new \RuntimeException('Could not load templates.');
		}
		while ($file = $templatesDir->read()) {
			if ($file != '.' && $file != '..') {
				$templateUrl = self::TEMPLATES_DIR . DIRECTORY_SEPARATOR . $file; // TODO: this should probably be a real URL
				$this->templates[$templateUrl] = new MicrodataTemplate($templateUrl);
			}
		}
		$templatesDir->close();
	}
	
	/**
	 * Get all templates RuleML facts
	 * 
	 * @return \UT\Hans\AutoMicrosite\RuleMl\RuleMl 
	 */
	public function getRuleMl() {
		$ruleMl = new RuleMl();
		foreach ($this->templates as $template) {
			$ruleMl->merge($template->toRuleMl());
		}
		return $ruleMl;
	}
	
	/**
	 * Get template object
	 * 
	 * @param type $templateUrl
	 * @return \UT\Hans\AutoMicrosite\Template\MicrodataTemplate|null 
	 */
	public function getTemplate($templateUrl) {
		if (isset($this->templates[$templateUrl])) {
			return $this->templates[$templateUrl];
		} else {
			return null;
		}
	}
	
}
