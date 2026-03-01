<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Ast;
use NaN\Database\Ast\Tree;

trait FromClauseTrait {
	use TableRefTrait;

	protected Tree $_from;

	public function from(string $table, ?string $database = null): static {
		$this->_from = $this->_createTableReference($table, $database);

		return $this;
	}

	protected function _pushFromClause(Tree $ast): void {
		if (\count($this->_from)) {
			$ast->push(Ast::clause([
				Ast::raw('FROM'),
				Ast::space(),
				$this->_from,
				Ast::space(),
			]));
		}
	}
}
