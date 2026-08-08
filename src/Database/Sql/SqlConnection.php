<?php

namespace NaN\Database\Sql;

use NaN\Database\Interfaces\ConnectionInterface;
use NaN\Database\Query\Builders\Interfaces\QueryBuilderInterface;
use NaN\Database\Sql\Query\Builders\SqlQueryBuilder;
use NaN\Database\Traits\ConnectionTrait;

class SqlConnection implements ConnectionInterface {
	use ConnectionTrait;

	public function getLastInsertId(): string | false {
		return $this->_connection->lastInsertId();
	}

	public function queryBuilder(): QueryBuilderInterface {
		return new SqlQueryBuilder();
	}
}
