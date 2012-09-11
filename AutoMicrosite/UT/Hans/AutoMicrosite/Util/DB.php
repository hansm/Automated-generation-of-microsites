<?php
namespace UT\Hans\AutoMicrosite\Util;

use PDO;

/**
 * Singleton wrapper for PDO with some additional features.
 *
 * @author Hans
 */
class DB extends PDO {

	private static $db;

	/**
	 * Return database object singleton
	 * @return \ut\hans\AutoMicrosite\Util\DB
	 * @throws \PDOException
	 */
	public static function get() {
		if (!isset(self::$db)) {
			self::$db = new DB();
		}
		return self::$db;
	}

	/**
	 * Prevent creation of new objects
	 */
	public function __construct() {
		parent::__construct('sqlite:'. ROOT .'DB/db_test.sdb');
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	/**
	 *
	 * @param string $sql
	 * @param array $variables
	 * @return array
	 * @throws \PDOException
	 */
	public function fetchRows($sql, $variables = null) {
		$query = $this->prepare($sql);
		$query->execute($variables);
		$rows = $query->fetchAll(PDO::FETCH_ASSOC);
		$query->closeCursor();
		return $rows;
	}
}

?>