<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Ast;
use NaN\Database\Ast\Tree;

trait IntoClauseTrait {
	use TableRefTrait;

	protected Tree $_target;

	public function into(string $table, string $database = ''): static {
		$this->_target = $this->_createTableReference($table, $database);

		return $this;
	}

	protected function _pushIntoClause(Tree $ast): void {
		if (isset($this->_target)) {
			$ast->push(Ast::clause([
				Ast::raw('INTO'),
				Ast::space(),
				$this->_target,
				Ast::space(),
			]));
		}
	}
}
