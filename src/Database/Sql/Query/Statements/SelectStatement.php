<?php

namespace NaN\Database\Sql\Query\Statements;

use NaN\Database\Ast;
use NaN\Database\Query\Statements\{
	Interfaces\StatementInterface,
	Traits\StatementTrait,
};
use NaN\Database\Sql\Query\Statements\{
	Traits\WhereClauseTrait,
};

final class SelectStatement implements StatementInterface {
	use StatementTrait;
	use WhereClauseTrait;

	public function distinct(): self {
		Ast::push(Ast::expr('distinct'), $this->_data);

		return $this;
	}

	public function select(array $columns = ['*']): self {
		$this->_data = Ast::node('select');
		$list = Ast::list();

		if (!empty($columns)) {
			foreach ($columns as $alias => $column) {
				if (!\is_numeric($alias)) {
					Ast::push(Ast::expr($column, 'AS', $alias), $list);
				} else {
					Ast::push(Ast::expr($column), $list);
				}
			}

			Ast::push($list, $this->_data);
		} else {
			throw new \InvalidArgumentException('Select statement must have at least one column!');
		}

		return $this;
	}
}
