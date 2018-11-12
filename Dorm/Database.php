<?php

namespace Dorm;

use \PDO;
use \Exception;

class Database {
	protected static $pdo;
	protected static $connected = false;

	public static function connect($args) {
		if (self::$connected) return;

		try {
			$host = $args['host'];
			$user = $args['user'];
			$password = $args['password'];
			$dbname = $args['dbname'];
			$dsn = 'mysql:host='.$host.';dbname='.$dbname;

			self::$pdo = new PDO($dsn, $user, $password);
			self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, 
				PDO::FETCH_OBJ);
			self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			self::$connected = true;
		} catch (PDOException $e) {
			echo "Dorm\Database::connect failed: ".
				$e->getMessage().PHP_EOL;
		}
	}

	public static function query($sql) {
		$result = self::$pdo->query($sql);
		if ($result) {
			return $result;
		} else {
			throw new Exception("Dorm\Database failed to execute query");
		}
	}

	public static function preparedQuery($sql, $args) {
		$stmt = self::$pdo->prepare($sql);
		if ($stmt->execute($args)) {
			return $stmt;
		} else {
			throw new Exception("Dorm\Database failed to execute query");
		}
	}

	public static function execute($sql, $args) {
		$stmt = self::$pdo->prepare($sql);
		if (!$stmt->execute($args)) {
			throw new Exception("Dorm\Database failed to execute query");
		}
	}

	public static function lastId() {
		return self::$pdo->lastInsertId();
	}
}

?>
