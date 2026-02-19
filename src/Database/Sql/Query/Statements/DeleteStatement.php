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
	Traits\OrderByTrait,
	Traits\WhereClauseTrait,
};

class DeleteStatement implements SqlStatementInterface {
	use SqlStatementTrait;
	use FromClauseTrait;
	use LimitClauseTrait;
	use OrderByTrait;
	use WhereClauseTrait;

	public function toAst(): Node {
		$ast = Ast::tree('delete', [
			Ast::raw('DELETE'),
			Ast::space(),
		]);

		if (\count($this->_from)) {
			$ast->push(Ast::raw('FROM'));
			$ast->push(Ast::space());
			$ast->push($this->_from);
			$ast->push(Ast::space());
		}

		return $ast;
	}
}
