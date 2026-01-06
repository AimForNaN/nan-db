<?php

namespace NaN\Database\Sql\Query\Statements;

use NaN\Database\Ast;
use NaN\Database\Query\Renderers\Interfaces\RendererInterface;
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

	public function __construct(
		protected RendererInterface $_renderer,
	) {
		$this->_data = Ast::tree('delete');
	}
}
