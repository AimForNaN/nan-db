<?php

namespace NaN\Database\Sql\Query\Statements;

use NaN\Database\Ast;
use NaN\Database\Sql\Query\Statements\{
	Interfaces\SqlStatementInterface,
	Traits\SqlStatementTrait,
};
use NaN\Database\Sql\Query\Statements\Clauses\{
	Traits\IntoClauseTrait,
	Traits\WhereClauseTrait,
};

class InsertStatement implements SqlStatementInterface {
	use SqlStatementTrait;
	use IntoClauseTrait;
	use WhereClauseTrait;

	public function insert(array $columns): self {
		$this->_data = Ast::tree('insert');

		if (!empty($columns)) {
			$insert_columns = Ast::list();
			$insert_values = Ast::tree('values');

			foreach ($columns as $column => $value) {
				$col = Ast::expr($column);
				$value = Ast::expr(value: $value);

				$insert_columns->push($col);
				$insert_values->push($value);
			}

			$group = Ast::group([$insert_columns]);

			$this->_data->push($group);
			$this->_data->push($insert_values);
		} else {
			throw new \InvalidArgumentException('Insert statement must have at least one column!');
		}

		return $this;
	}
}
