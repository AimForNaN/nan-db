<?php

namespace NaN\Database\Sql\Query\Builders;

use NaN\Database\Query\Builders\Interfaces\QueryBuilderInterface;
use NaN\Database\Sql\Query\{
	Statements\DeleteStatement,
	Statements\InsertStatement,
	Statements\SelectStatement,
	Statements\UpdateStatement,
};

class SqlQueryBuilder implements QueryBuilderInterface {
	public function patch(string $table_ref = ''): UpdateStatement {
		$query = new UpdateStatement();

		if (!empty($table_ref)) {
			$query->update($table_ref);
		}

		return $query;
	}

	public function pull(array $selection = []): SelectStatement {
		$query = new SelectStatement();

		if (!empty($selection)) {
			$query->select($selection);
		} else {
			$query->select();
		}

		return $query;
	}

	public function purge(string $table_ref = ''): DeleteStatement {
		$query = new DeleteStatement();

		if (!empty($table_ref)) {
			$query->from($table_ref);
		}

		return $query;
	}

	public function push(array $columns = []): InsertStatement {
		$query = new InsertStatement();

		if (!empty($columns)) {
			$query->insert($columns);
		}

		return $query;
	}
}
