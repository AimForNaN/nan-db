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

	protected array $_columns = [];
	protected bool $_distinct = false;

	public function last(string $column): self {
		return $this->orderBy([$column => 'desc'])->limit(1);
	}

	public function select(array $columns = ['ALL']): self {
		if (empty($columns)) {
			throw new \InvalidArgumentException('Select statement must have at least one column!');
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

		if (\count($this->_from)) {
			$ast->push(Ast::clause([
				Ast::raw('FROM'),
				Ast::space(),
				$this->_from,
				Ast::space(),
			]));
		}

		if (isset($this->_where) && \count($this->_where)) {
			$ast->push(Ast::clause([
				Ast::raw('WHERE'),
				Ast::space(),
				$this->_where->toAst(),
				Ast::space(),
			]));
		}

		if (\count($this->_group_by)) {
			$group_by = Ast::list(\array_map(fn($column) => Ast::identifier($column), $this->_group_by));

			$ast->push(Ast::clause([
				Ast::raw('GROUP BY'),
				Ast::space(),
				$group_by,
				Ast::space(),
			]));
		}

		if (\count($this->_order_by)) {
			$order_by = Ast::list();

			foreach ($this->_order_by as $column => $direction) {
				$expr = Ast::expression([
					Ast::identifier($column),
					Ast::space(),
					Ast::value(\strtoupper($direction)),
				]);

				$order_by->push($expr);
			}

			$ast->push(Ast::clause([
				Ast::raw('ORDER BY'),
				Ast::space(),
				$order_by,
				Ast::space(),
			]));
		}

		if ($this->_limit > 0) {
			$limit = Ast::clause([
				Ast::raw('LIMIT'),
				Ast::space(),
				Ast::value($this->_limit),
				Ast::space(),
			]);

			$ast->push($limit);
		}

		if ($this->_offset > 0) {
			$offset = Ast::clause([
				Ast::raw('OFFSET'),
				Ast::space(),
				Ast::value($this->_offset),
				Ast::space(),
			]);

			$ast->push($offset);
		}

		return $ast;
	}
}
