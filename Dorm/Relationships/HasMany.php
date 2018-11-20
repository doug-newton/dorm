<?php

namespace Dorm\Relationships;

use Dorm\Relationship;

class HasMany extends Relationship {
	public function get() {
		return $this->child_class::where([
			$this->foreign_key => $this->owner->getId()
		]);
	}
}

?>
