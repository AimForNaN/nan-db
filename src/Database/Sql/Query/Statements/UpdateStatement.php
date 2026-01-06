<?php

namespace NaN\Database\Sql\Query\Statements;

use NaN\Database\Ast;
use NaN\Database\Sql\Query\Statements\{
	Interfaces\SqlStatementInterface,
	Traits\SqlStatementTrait,
};
use NaN\Database\Sql\Query\Statements\Clauses\{
	Traits\FromClauseTrait,
	Traits\LimitClauseTrait,
	Traits\OrderByTrait,
	Traits\TableRefTrait,
	Traits\WhereClauseTrait,
};

class UpdateStatement implements SqlStatementInterface {
	use SqlStatementTrait;
	use FromClauseTrait;
	use LimitClauseTrait;
	use OrderByTrait;
	use TableRefTrait;
	use WhereClauseTrait;

	public function update(string $table, ?string $database = null): static {
		$this->_data = $this->_createTableReference('update', $table, $database);

		return $this;
	}

	public function with(array $columns): static {
		$set = Ast::tree('set');

		foreach ($columns as $column => $value) {
			$column = Ast::expr($column, '=', $value);

			$set->push($column);
		}

		$this->_data->push($set);

		return $this;
	}
}

