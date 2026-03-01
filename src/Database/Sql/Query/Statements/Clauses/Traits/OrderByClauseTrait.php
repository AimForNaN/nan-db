<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Ast;
use NaN\Database\Ast\Tree;

trait OrderByClauseTrait {
	protected array $_order_by = [];

	public function orderBy(array $order_by): static {
		if (empty($order_by)) {
			throw new \InvalidArgumentException('Order-by columns must not be empty!');
		}

		$this->_order_by = $order_by;

		return $this;
	}

	protected function _pushOrderByClause(Tree $ast): void {
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
	}
}
