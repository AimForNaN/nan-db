<?php

namespace NaN\Database\Traits;

use NaN\Database\Interfaces\EntityInterface;
use NaN\Database\Query\Statements\{
	Patch,
	Pull,
	Purge,
	Push,
};

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

	public function fill(iterable $data) {
		foreach ($data as $column => $value) {
			$this->$column = $value;
		}
	}

	protected function getMapping($name): ?string {
		$mappings = static::mappings();
		return $mappings[$name] ?? null;
	}

	public function patch(): mixed {
		$db = static::database();
		$patch = new Patch();

		return $db->exec($patch);
	}

	static public function pull(callable $fn): mixed {
		$db = static::database();
		$pull = new Pull();

		$fn($pull);
		$pull->pull(['*'])->from($db['table'], $db['database']);

		return $db->exec($pull);
	}

	public function purge(callable $fn): mixed {
		$db = static::database();
		$purge = new Purge();

		$fn($purge);

		return $db->exec($purge);
	}

	static public function push(array $data): mixed {
		$db = static::database();
		$push = new Push();

		return $db->exec($push);
	}
}
