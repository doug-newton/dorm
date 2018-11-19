<?php

namespace Dorm;

use Dorm\Database;
use Dorm\QueryBuilder;
use \PDO;

abstract class Model {
	#	auto-generated id of the model (once 'created' or 'saved')
	protected $id = 0;

	#	name of the model's table
	protected static $table = '';

	#	fields allowed to be used in update and insert queries
	protected static $fillable = [];

	#	default values used in both create and save
	protected static $defaults = [];

	#	all records
	public static function all() {
		$objects = [];
		$class = get_called_class();
		$table = $class::$table;

		$sql = "select * from $table;";

		foreach (Database::query($sql)->fetchAll(PDO::FETCH_ASSOC) as $data) {
			$object = new $class;
			$object->input($data);
			$object->setId($data['id']);
			$objects[] = $object;
		}

		return $objects;
	}

	#	finds record with given parameters
	public static function where($args) {
		$class = get_called_class();
		$object = new $class;
		$builder = new QueryBuilder();
		$sql = $builder->build_where($object::$table, $args);
		$data = Database::preparedQuery($sql, $args)->fetch(PDO::FETCH_ASSOC);
		$object->input($data);
		$object->setId($data['id']);
		return $object;
	}

	#	finds record and returns new instantiated object
	public static function find($id) {
		$class = get_called_class();
		$object = new $class;
		$builder = new QueryBuilder();
		$sql = $builder->build_select($object::$table, $id);
		$object->input(Database::query($sql)->fetch(PDO::FETCH_ASSOC));
		$object->setId($id);
		return $object;
	}

	#	instantiates new object and saves it to database
	public static function create($array) {
		$class = get_called_class();
		$object = new $class;
		$builder = new QueryBuilder();
		$sql = $builder->build_insert(
			$object::$table,
			$object::$fillable,
			$object::$defaults,
			$array
		);
		$object->input($array);
		Database::preparedQuery($sql, $array);
		$object->setId(Database::lastId());
		return $object;
	}

	#	instantiates new object without saving it to database
	public static function new($array) {
		$class = get_called_class();
		$object = new $class;
		$object->input($array);
		return $object;
	}

	#	either inserts the object or brute-updates its record values
	public function save() {
		#	insert query if this has no id
		if ($this->id == 0) {
			$builder = new QueryBuilder();
			$array = $this->output();
			$sql = $builder->build_insert(
				$this::$table,
				$this::$fillable,
				$this::$defaults,
				$array
			);
			Database::preparedQuery($sql, $array);
			#	apply defaults
			$this->input($array);
			$this->setId(Database::lastId());
		}
		#	update query if it has id
		else {
			$builder = new QueryBuilder();
			$array = $this->output();
			$sql = $builder->build_update(
				$this::$table,
				$this->id,
				$this::$fillable,
				$this::$defaults,
				$array
			);
			Database::preparedQuery($sql, $array);
			#	apply defaults
			$this->input($array);
		}
	}

	protected function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	#	assign values to object from array
	protected function input($data) {
		foreach($data as $key => $value) {
			if (in_array($key, $this::$fillable)) {
				$this->$key = $value;
			}
		}
	}

	#	create associative array from object attributes
	protected function output() {
		$output = get_object_vars($this);
		foreach ($output as $key => $value) {
			if (!in_array($key, $this::$fillable)) {
				unset($output[$key]);
			}
		}
		return $output;
	}

	public function load($data) {
		$this->input($data);
		$this->setId($data['id']);
	}
}

?>
