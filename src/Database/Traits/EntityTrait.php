<?php

namespace NaN\Database\Traits;

trait EntityTrait {
	public function __get(string $name) {
		$column = $this->getMapping($name);
		if ($column) {
			return $this->$column;
		} else {
			\trigger_error("Non-existent property '$name'!", E_USER_WARNING);
		}

		return null;
	}

	public function __set(string $name, mixed $value) {
		$column = $this->getMapping($name);
		if ($column) {
			$this->$column = $value;
		} else {
			\trigger_error("Non-existent property '$name'!", E_USER_WARNING);
		}
	}

	public function fill(iterable $data): void {
		foreach ($data as $column => $value) {
			$this->$column = $value;
		}
	}

	protected function getMapping($name): ?string {
		$mappings = static::mappings();
		return $mappings[$name] ?? null;
	}
}
