<?php

require_once 'vendor/autoload.php';

use Dorm\Database;
use Dorm\QueryBuilder;
use Dorm\Model;

Database::connect([
	'host' => 'localhost',
	'user' => 'root',
	'password' => 'root',
	'dbname' => 'dorm'
]);

class Car extends Model {
	protected static $table = 'cars';
	protected static $fillable = ['user_id','name'];
	protected static $defaults = ['user_id' => 0];

	public function user() {
		$builder = new QueryBuilder();
	}
}

class User extends Model {
	protected static $table = 'users';
	protected static $fillable = ['name','email'];

	public function car() {
		return $this->hasOne(Car::class, 'user_id');
	}

	public function cars() {
		return $this->hasMany(Car::class, 'user_id');
	}
}

try {
	$user = User::where([
		'name' => 'Alfonso'
	])[0];

	foreach ($user->cars as $car) {
		echo "$car->name".PHP_EOL;
	}

} catch (Exception $e) {
	echo "there was an exception";
}

?>
