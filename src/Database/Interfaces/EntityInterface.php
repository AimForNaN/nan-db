<?php

namespace NaN\Database\Interfaces;

use NaN\Database\Query\Builders\Interfaces\QueryBuilderInterface;

interface EntityInterface {
	static public function database(): QueryBuilderInterface;

	static public function mappings(): array;

	public function patch(): mixed;

	static public function pull(array $filters): mixed;

	public function purge(): mixed;

	static public function push(array $data): mixed;
}