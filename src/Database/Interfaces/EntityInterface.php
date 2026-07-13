<?php

namespace NaN\Database\Interfaces;

interface EntityInterface extends \JsonSerializable {
	public string $id { get; }

	public static function fromArray(iterable $data): EntityInterface;

	public function withId(string $id): EntityInterface;
}
