<?php

namespace NaN\Database\Sql\Query\Statements;

use NaN\Database\Ast;
use NaN\Database\Ast\Node;
use NaN\Database\Sql\Query\Statements\{
	Interfaces\SqlStatementInterface,
	Traits\SqlStatementTrait,
};
use NaN\Database\Sql\Query\Statements\Clauses\{
	Traits\FromClauseTrait,
	Traits\GroupByClauseTrait,
	Traits\LimitClauseTrait,
	Traits\OrderByClauseTrait,
	Traits\WhereClauseTrait,
};

final class SelectStatement implements SqlStatementInterface {
	use SqlStatementTrait;
	use FromClauseTrait;
	use GroupByClauseTrait;
	use LimitClauseTrait;
	use OrderByClauseTrait;
	use WhereClauseTrait;

	protected array $_columns = [];
	protected bool $_distinct = false;

	public function last(string $column): self {
		return $this->orderBy([$column => 'desc'])->limit(1);
	}

	public function select(array $columns = ['ALL']): self {
		if (empty($columns)) {
			throw new \ValueError('Select statement must have at least one column!');
		}

		$this->_columns = $columns;

		return $this;
	}

	public function toAst(): Node {
		$ast = Ast::tree('select', [
			Ast::raw('SELECT'),
			Ast::space(),
		]);

		if ($this->_distinct) {
			$ast->push(Ast::clause([
				Ast::raw('DISTINCT'),
				Ast::space(),
			]));
		}

		if (!empty($this->_columns)) {
			$list = Ast::list();

			foreach ($this->_columns as $alias => $column) {
				$expr = Ast::expression();

				if ($column === 'ALL' || $column === '*') {
					$ast->push(Ast::raw($column));
					$ast->push(Ast::space());
				} else {
					$expr->push(Ast::identifier($column));
					$expr->push(Ast::space());
				}

				if (!\is_numeric($alias)) {
					$expr->push(Ast::raw('AS'));
					$expr->push(Ast::space());
					$expr->push(Ast::identifier($alias));
					$expr->push(Ast::space());
				}

				$list->push($expr);
			}

			$ast->push($list);
		}

		$this->_pushFromClause($ast);
		$this->_pushWhereClause($ast);
		$this->_pushGroupByClause($ast);
		$this->_pushOrderByClause($ast);
		$this->_pushLimitClause($ast);

		return $ast;
	}
}
