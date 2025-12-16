<?php

namespace NaN\Database\Interfaces;

interface EntityInterface {
	public function fill(iterable $data);

	/**
	 * @deprecated Replaced by property hooks.
	 * @return array
	 */
	public static function mappings(): array;
}
