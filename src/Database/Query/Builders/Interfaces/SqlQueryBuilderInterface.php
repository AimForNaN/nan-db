<?php

namespace NaN\Database\Query\Builders\Interfaces;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;

interface SqlQueryBuilderInterface extends QueryBuilderInterface {
	public function exec(StatementInterface $statement): mixed;

	public function getLastInsertId(): string | false;

	public function raw(string $query, array $bindings = []): mixed;

	public function transact(callable $fn): bool;
}