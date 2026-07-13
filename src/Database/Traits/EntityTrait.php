<?php

namespace NaN\Database\Traits;

use NaN\Database\Interfaces\EntityInterface;

/**
 * @implements EntityInterface
 */
trait EntityTrait {
	public static function fromArray(iterable $data): EntityInterface {
		$new = new static();

		foreach ($data as $column => $value) {
			$new->$column = $value;
		}

		return $new;
	}

	public function jsonSerialize(): array {
		return \array_map(function ($value) {
			if ($value instanceof \BackedEnum) {
				return $value->value;
			}

			return $value;
		}, (array)$this);
	}

	public function withId(string $id): EntityInterface {
		$clone = clone $this;

		$clone->id = $id;

		return $clone;
	}
}
