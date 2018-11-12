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


# insert into table
# (one, two, three, four)
# values
# ('qwerty', 'uiop', 'asdfg', 'ghoti');


use Dorm\QueryBuilder;

$builder = new QueryBuilder();

$fillable = ['one', 'two', 'three', 'four'];

class User extends Model {
	protected static $table = 'users';
	protected static $fillable = ['name', 'email'];
	protected static $defaults = [
		'name' => "No Name",
		'email' => "nomail@example.com"
	];

	private $name;
	private $email;

	public function __construct() {
	}

	protected function input($data) {
		$this->name = $data['name'];
		$this->email = $data['email'];
	}

	protected function output() {
		return [
			'name' => $this->name,
			'email' => $this->email
		];
	}

	#	accessors

	public function getName() {
		return $this->name;
	}

	public function getEmail() {
		return $this->email;
	}

	#	mutators

	public function setName($name) {
		$this->name = $name;
	}

	public function setEmail($email) {
		$this->email = $email;
	}
}

class Foo extends Model {
	protected static $table = 'foos';
	protected static $fillable = ['bar', 'baz'];
	protected static $defaults = [
		'bar' => "default_bar",
		'baz' => "default_baz"
	];

	private $bar;
	private $baz;

	public function __construct() {
	}

	protected function input($data) {
		$this->bar = $data['bar'];
		$this->baz = $data['baz'];
	}

	protected function output() {
		return [];
	}
}

?>
