<?php

namespace NaN\Database\Sql\Query\Builders;

use NaN\Database\Query\Builders\Interfaces\QueryBuilderInterface;
use NaN\Database\Query\Statements\{Patch, Purge, Push};
use NaN\Database\Query\Statements\Interfaces\StatementInterface;
use NaN\Database\Sql\Query\{
	Renderers\SqlQueryRenderer,
	Statements\SelectStatement,
};

class SqlQueryBuilder implements QueryBuilderInterface {
	/**
	 * @throws \Exception
	 */
	public function patch(callable $fn = null): StatementInterface {
		$query = new Patch();

		if ($fn) {
			$fn($query);
		}

		return $query;
	}

	public function pull(callable $fn = null): StatementInterface {
		$query = new SelectStatement(new SqlQueryRenderer());

		if ($fn) {
			$fn($query);
		}

		return $query;
	}

	/**
	 * @throws \Exception
	 */
	public function purge(callable $fn = null): StatementInterface {
		$query = new Purge();

		if ($fn) {
			$fn($query);
		}

		return $query;
	}

	/**
	 * @throws \Exception
	 */
	public function push(callable $fn = null): StatementInterface {
		$query = new Push();

		if ($fn) {
			$fn($query);
		}

		return $query;
	}
}
