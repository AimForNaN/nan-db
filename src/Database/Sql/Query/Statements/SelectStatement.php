<?php

namespace NaN\Database\Sql\Query\Statements;

use NaN\Database\Ast;
use NaN\Database\Quotes;
use NaN\Database\Sql\Query\Statements\{
	Interfaces\SqlStatementInterface,
	Traits\SqlStatementTrait,
};
use NaN\Database\Sql\Query\Statements\Clauses\{
	Traits\FromClauseTrait,
	Traits\GroupByTrait,
	Traits\LimitClauseTrait,
	Traits\OrderByTrait,
	Traits\WhereClauseTrait,
};

final class SelectStatement implements SqlStatementInterface {
	use SqlStatementTrait;
	use FromClauseTrait;
	use GroupByTrait;
	use LimitClauseTrait;
	use OrderByTrait;
	use WhereClauseTrait;

	public function select(array $columns = ['ALL'], bool $distinct = false): self {
		$this->_data = Ast::tree('select');

		if ($distinct) {
			$this->_data->push(Ast::expr('DISTINCT', quotes: [Quotes::None]));
		}

		$list = Ast::list();

		if (!empty($columns)) {
			foreach ($columns as $alias => $column) {
				$quotes = [Quotes::Backtick];

				if ($column === 'ALL' || $column === '*') {
					$quotes = [Quotes::None];
				}

				$expr = Ast::expr($column, quotes: $quotes);

				if (!\is_numeric($alias)) {
					$expr = Ast::expr($column, 'AS', $alias, [Quotes::Backtick, Quotes::None, Quotes::Backtick]);
				}

				$list->push($expr);
			}

			$this->_data->push($list);
		} else {
			throw new \InvalidArgumentException('Select statement must have at least one column!');
		}

		return $this;
	}

	public function last(string $column): self {
		return $this->orderBy([$column => 'desc'])->limit(1);
	}
}
