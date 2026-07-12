<?php

namespace NaN\Database\Sql\Query\Statements;

use NaN\Database\Ast;
use NaN\Database\Ast\Node;
use NaN\Database\Sql\Query\Statements\{
	Interfaces\SqlStatementInterface,
	Traits\SqlStatementTrait,
};
use NaN\Database\Quotes;
use NaN\Database\Sql\Prepare;
use NaN\Database\Sql\Query\Statements\Clauses\{
	Traits\IntoClauseTrait,
	Traits\WhereClauseTrait,
};

class InsertStatement implements SqlStatementInterface {
	use SqlStatementTrait;
	use IntoClauseTrait;
	use WhereClauseTrait;

	protected array $_columns = [];

	public function insert(array $columns): self {
		if (empty($columns)) {
			throw new \ValueError('Insert statement must have at least one column!');
		}

		$this->_columns = $columns;

		return $this;
	}

	public function toAst(): Node {
		$ast = Ast::tree('insert', [
			Ast::raw('INSERT'),
			Ast::space(),
		]);

		$this->_pushIntoClause($ast);

		if (\count($this->_columns)) {
			$insert_columns = Ast::list();

			foreach ($this->_columns as $column => $value) {
				$col = Ast::identifier($column);

				$insert_columns->push($col);
			}

			$ast->push(Ast::group([$insert_columns]));
			$ast->push(Ast::space());

			$ast->push(Ast::clause([
				Ast::raw('VALUES'),
				Ast::space(),
				Ast::value($this->_columns, Quotes::Auto, Prepare::All),
			]));
		}

		$this->_pushWhereClause($ast);

		return $ast;
	}
}
