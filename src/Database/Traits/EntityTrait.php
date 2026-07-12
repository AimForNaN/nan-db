<?php

namespace NaN\Database\Traits;

use NaN\Database\Interfaces\EntityInterface;

/**
 * @implements EntityInterface
 */
trait EntityTrait {
	private(set) string $id;

	public static function fromArray(iterable $data): EntityInterface {
		$new = new static();

		foreach ($data as $column => $value) {
			$new->$column = $value;
		}

		return $new;
	}

	public function withId(string $id): EntityInterface {
		$clone = clone $this;

		$clone->id = $id;

		return $clone;
	}
}
