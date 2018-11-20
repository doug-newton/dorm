<?php

namespace Dorm;

use Dorm\Database;
use Dorm\QueryBuilder;
use \PDO;
use \Exception;

abstract class Relationship {
	public abstract function get();

	protected $child_class;
	protected $foreign_key;

	# the object that is using this relationship
	protected $owner;

	public function __construct($owner, $child_class, $foreign_key) {
		$this->owner = $owner;
		$this->child_class = $child_class;
		$this->foreign_key = $foreign_key;
	}
}

class HasOne extends Relationship {
	public function get() {
		# same as hasMany, but returns first result and there must only be one
		$data = $child_class::where([
			$this->foreign_key => $this->owner->getId()
		]);

		if (sizeof($data) != 1) {
			throw new Exception("Dorm\Model \"".get_class($this->owner).
				"\" hasOne ".
				"failed: parent must have one child");
		}

		return $data[0];
	}
}

class HasMany extends Relationship {
	public function get() {
		return $this->child_class::where([
			$this->foreign_key => $this->owner->getId()
		]);
	}
}

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
		$builder = new QueryBuilder();
		$sql = $builder->build_where($class::$table, $args);
		$data = Database::preparedQuery($sql,$args)->fetchAll(PDO::FETCH_ASSOC);

		$objects = [];
		foreach ($data as $info) {
			$object = new $class;
			$object->input($info);
			$object->setId($info['id']);
			$objects[] = $object;
		}

		return $objects;
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
		if ($data === false) {
			throw new Exception("Dorm\Model \"".get_class($this).
				"\" cannot read data for input");
		}

		foreach($data as $key => $value) {
			if (in_array($key, $this::$fillable)) {
				$this->$key = $value;
			}
		}

		$this->preLoad();
	}

	protected function preLoad() {
	}

	#	create associative array from object attributes
	protected function output() {
		$output = get_object_vars($this);
		foreach ($output as $key => $value) {
			if (!in_array($key, $this::$fillable)) {
				unset($output[$key]);
			}
		}
		return $this->preSave($output);
	}

	protected function preSave(&$data) {
		return $data; 
	} 
	
	public function load($data) {
		$this->input($data);
		$this->setId($data['id']);
	}

	# for lazy relational loading
	public function __get($name) {
		if (method_exists($this, $name)) {
			$result = $this->{$name}();

			# if the returned structure has a get method (is relation)
			# then use the result of the get method

			if (is_object($result)) {
				if (method_exists($result, 'get')) {
					return $this->$name = $result->get();
				}
			}

			return $this->$name = $result;
		}
	}

	# relationships

	public function hasOne($child_class, $foreign_key) {
		return new HasOne($this, $child_class, $foreign_key);
	}

	public function hasMany($child_class, $foreign_key) {
		return new HasMany($this, $child_class, $foreign_key);
	}
}

?>
