<?php

namespace NaN\Database\Sql\Query\Builders;

use NaN\Database\Query\Builders\Interfaces\QueryBuilderInterface;
use NaN\Database\Query\Statements\Interfaces\StatementInterface;
use NaN\Database\Sql\Query\{
	Renderers\SqlQueryRenderer,
	Statements\DeleteStatement,
	Statements\InsertStatement,
	Statements\SelectStatement,
	Statements\UpdateStatement,
};

class SqlQueryBuilder implements QueryBuilderInterface {
	/**
	 * @throws \Exception
	 */
	public function patch(?callable $fn = null): StatementInterface {
		$query = new UpdateStatement(new SqlQueryRenderer());

		if ($fn) {
			$fn($query);
		}

		return $query;
	}

	public function pull(?callable $fn = null): StatementInterface {
		$query = new SelectStatement(new SqlQueryRenderer());

		if ($fn) {
			$fn($query);
		}

		return $query;
	}

	/**
	 * @throws \Exception
	 */
	public function purge(?callable $fn = null): StatementInterface {
		$query = new DeleteStatement(new SqlQueryRenderer());

		if ($fn) {
			$fn($query);
		}

		return $query;
	}

	/**
	 * @throws \Exception
	 */
	public function push(?callable $fn = null): StatementInterface {
		$query = new InsertStatement(new SqlQueryRenderer());

		if ($fn) {
			$fn($query);
		}

		return $query;
	}
}
