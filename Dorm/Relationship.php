<?php

namespace Dorm;

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

?>
