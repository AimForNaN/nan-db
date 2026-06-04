<?php

namespace NaN\Database\Interfaces;

use NaN\Database\Query\Builders\Interfaces\QueryBuilderInterface;
use NaN\Database\Query\Statements\Interfaces\StatementInterface;

interface ConnectionInterface {
	public function close(): void;

	public function exec(StatementInterface $query): mixed;

	public function queryBuilder(): QueryBuilderInterface;

	public function raw(string $query): mixed;
}
