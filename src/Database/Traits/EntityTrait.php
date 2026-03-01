<?php

namespace NaN\Database\Traits;

trait EntityTrait {
	public function fill(iterable $data): void {
		foreach ($data as $column => $value) {
			$this->$column = $value;
		}
	}
}
