<?php

namespace Dorm;

use Dorm\Database;
use Dorm\QueryBuilder;

abstract class Model {
	protected $id = 0;
	protected $table = '';
	protected $fillable = [];
	protected $defaults = [];

	public static function create($array) {
		$class = get_called_class();
		$object = new $class;
		$builder = new QueryBuilder();
		$sql = $builder->build_insert(
			$object->table,
			$object->fillable,
			$object->defaults,
			$array
		);
		$object->input($array);
		Database::preparedQuery($sql, $array);
		$object->setId(Database::lastId());
		return $object;
	}

	public function save() {
		#	insert query if this has no id
		if ($this->id == 0) {
			$builder = new QueryBuilder();
			$array = $this->output();
			$sql = $builder->build_insert(
				$this->table,
				$this->fillable,
				$this->defaults,
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
				$this->table,
				$this->id,
				$this->fillable,
				$this->defaults,
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
	abstract protected function input($data);

	#	create associative array from object attributes
	abstract protected function output();
}

?>
