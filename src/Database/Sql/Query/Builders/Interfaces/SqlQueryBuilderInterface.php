<?php

namespace NaN\Database\Sql\Query\Builders\Interfaces;

use NaN\Database\Query\Builders\Interfaces\QueryBuilderInterface;

interface SqlQueryBuilderInterface extends QueryBuilderInterface {
	public function getLastInsertId(): string | false;

	public function transact(callable $fn): bool;
}
