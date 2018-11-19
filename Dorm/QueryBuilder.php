<?php

namespace Dorm;

use \Exception;

class QueryBuilder {
	function build_insert($table, $fillable, $defaults, &$array) {
		foreach ($defaults as $key => $value) {
			if (!isset($array[$key])) {
				$array[$key] = $value;
			}
		}
		$sql = "insert into $table (";
		$size = sizeof($array);
		$i = 1;
		foreach ($array as $field => $value) {
			if (!in_array($field, $fillable)) {
				throw new Exception("QueryBuilder failed: illegal field");
			}
			$sql .= $field;
			if ($i == $size) {
				$sql .= ")";
			} else $sql .= ", ";
			$i ++;
		}
		$sql .= " values (";
		$i = 1;
		foreach ($array as $field => $value) {
			$sql .= ":$field";
			if ($i == $size) {
				$sql .= ")";
			} else $sql .= ", ";
			$i ++;
		}
		$sql .= ";";
		return $sql;
	}

	function build_update($table, $id, $fillable, $defaults, &$array) {
		foreach ($defaults as $key => $value) {
			if (!isset($array[$key])) {
				$array[$key] = $value;
			}
		}
		$sql = "update $table set ";
		$size = sizeof($array);
		$i = 1;
		foreach ($array as $field => $value) {
			if (!in_array($field, $fillable)) {
				throw new Exception("QueryBuilder failed: illegal field");
			}
			$sql .= "$field = :$field";
			if ($i != $size) $sql .= ", ";
			$i ++;
		}
		$sql .= " where id = $id;";
		return $sql;
	}

	function build_select($table, $id) {
		return "select * from $table where id = $id;";
	}

	function build_where($table, $args) {
		$sql = "select * from $table where ";
		$size = sizeof($args);
		$i = 1;
		foreach ($args as $field => $value) {
			$sql .= "$field = :$field";
			if ($i != $size) $sql .= " and ";
			$i ++;
		}
		$sql .= ";";
		return $sql;
	}
}

?>
