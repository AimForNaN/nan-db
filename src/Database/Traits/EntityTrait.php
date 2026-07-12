<?php

namespace NaN\Database\Traits;

use NaN\Database\Interfaces\EntityInterface;

trait EntityTrait {
	public static function fromArray(iterable $data): EntityInterface {
		$new = new static();

		foreach ($data as $column => $value) {
			$new->$column = $value;
		}

		return $new;
	}
}
