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

	# sets the foreign key of the child
	public function associate($object) {
		if (get_class($object) != $this->child_class) {
			throw new Exception("Dorm\Relationship association failed".
				": invalid object class supplied");
		}
		$object->{$this->foreign_key} = $this->owner->getId();
	}
}

?>
