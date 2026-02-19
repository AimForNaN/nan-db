<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Ast;
use NaN\Database\Ast\Tree;

trait TableRefTrait {
	/**
	 * @throws \InvalidArgumentException If table is not provided!
	 */
	protected function _createTableReference(string $table, ?string $database = null): Tree {
		if (empty($table)) {
			throw new \InvalidArgumentException('Table reference is required!');
		}

		$expr = Ast::expression([
			Ast::identifier($table),
		]);

		if (!empty($database)) {
			$expr->push(Ast::raw('.'));
			$expr->push(Ast::identifier($database));
		}

		return $expr;
	}
}
