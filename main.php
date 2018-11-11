<?php

namespace Dorm;

use \PDO;

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
}

Database::connect([
	'host' => 'localhost',
	'user' => 'root',
	'password' => 'root',
	'dbname' => 'dorm'
]);

?>
