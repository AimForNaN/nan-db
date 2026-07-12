<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Ast;
use NaN\Database\Ast\Tree;

trait GroupByClauseTrait {
	protected array $_group_by = [];

	public function groupBy(array $columns): static {
		if (empty($columns)) {
			throw new \ValueError('Group-by columns must not be empty!');
		}

		$this->_group_by = $columns;

		return $this;
	}

	protected function _pushGroupByClause(Tree $ast): void {
		if (\count($this->_group_by)) {
			$group_by = Ast::list(\array_map(fn($column) => Ast::identifier($column), $this->_group_by));

			$ast->push(Ast::clause([
				Ast::raw('GROUP BY'),
				Ast::space(),
				$group_by,
				Ast::space(),
			]));
		}
	}
}
