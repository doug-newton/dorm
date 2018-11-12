<?php

require_once 'vendor/autoload.php';

use Dorm\Database;
use Dorm\Model;

Database::connect([
	'host' => 'localhost',
	'user' => 'root',
	'password' => 'root',
	'dbname' => 'dorm'
]);

?>
