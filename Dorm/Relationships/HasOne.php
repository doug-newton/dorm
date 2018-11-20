<?php

namespace Dorm\Relationships;

use Dorm\Relationship;

class HasOne extends Relationship {
	public function get() {
		# same as hasMany, but returns first result and there must only be one
		$data = $child_class::where([
			$this->foreign_key => $this->owner->getId()
		]);

		if (sizeof($data) != 1) {
			throw new Exception("Dorm\Model \"".get_class($this->owner).
				"\" hasOne ".
				"failed: parent must have at least 1 child ".
				"to be assumed as the only one");
		}

		return $data[0];
	}
}

?>
