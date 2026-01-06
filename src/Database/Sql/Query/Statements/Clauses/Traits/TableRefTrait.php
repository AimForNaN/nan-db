<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Ast;
use NaN\Database\Ast\Tree;
use NaN\Database\Quotes;

trait TableRefTrait {
	/**
	 * @throws \InvalidArgumentException If table is not provided!
	 */
	protected function _createTableReference(string $clause, string $table, ?string $database = null): Tree {
		if (empty($table)) {
			throw new \InvalidArgumentException('Table reference is required!');
		}

		$ref = Ast::tree($clause);
		$expr = Ast::expr($table, quotes: [Quotes::Backtick]);

		if (!empty($database)) {
			$expr = Ast::expr($table, '.', $database, [Quotes::Backtick, Quotes::None, Quotes::Backtick]);
		}

		$ref->push($expr);

		return $ref;
	}
}
