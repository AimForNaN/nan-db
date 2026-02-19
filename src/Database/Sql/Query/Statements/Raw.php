<?php

namespace NaN\Database\Sql\Query\Statements;

use NaN\Database\Ast;
use NaN\Database\Ast\Node;

class Raw implements Interfaces\SqlStatementInterface {
	use Traits\SqlStatementTrait;

	public function __construct(
		protected string $_sql,
		protected array $_bindings = [],
	) {
	}

	public function getBindings(): array {
		return $this->_bindings;
	}

	public function toAst(): Node {
		return Ast::raw($this->_sql);
	}
}
