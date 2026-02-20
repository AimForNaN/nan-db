<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Ast;
use NaN\Database\Ast\Tree;
use NaN\Database\Sql\Query\Statements\Clauses\WhereClause;

trait WhereClauseTrait {
	protected WhereClause $_where;
	public function where(\Closure $fn): static {
		$this->_where = new WhereClause();

		$fn($this->_where);

		return $this;
	}

	protected function _pushWhereClause(Tree $ast): void {
		if (isset($this->_where) && \count($this->_where)) {
			$ast->push(Ast::clause([
				Ast::raw('WHERE'),
				Ast::space(),
				$this->_where->toAst(),
				Ast::space(),
			]));
		}

	}
}
