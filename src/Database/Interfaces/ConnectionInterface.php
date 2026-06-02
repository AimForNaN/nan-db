<?php

namespace NaN\Database\Interfaces;

use NaN\Database\Query\Builders\Interfaces\QueryBuilderInterface;

interface ConnectionInterface {
	public function close(): void;

	public function queryBuilder(): QueryBuilderInterface;

	public function raw(string $query): mixed;
}
