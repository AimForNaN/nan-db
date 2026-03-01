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
	Traits\LimitClauseTrait,
	Traits\OrderByClauseTrait,
	Traits\WhereClauseTrait,
};

class DeleteStatement implements SqlStatementInterface {
	use SqlStatementTrait;
	use FromClauseTrait;
	use LimitClauseTrait;
	use OrderByClauseTrait;
	use WhereClauseTrait;

	public function toAst(): Node {
		$ast = Ast::tree('delete', [
			Ast::raw('DELETE'),
			Ast::space(),
		]);

		$this->_pushFromClause($ast);

		$this->_pushWhereClause($ast);

		$this->_pushOrderByClause($ast);

		$this->_pushLimitClause($ast);

		return $ast;
	}
}
