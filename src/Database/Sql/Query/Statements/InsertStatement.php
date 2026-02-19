<?php

namespace NaN\Database\Sql\Query\Statements;

use NaN\Database\Ast;
use NaN\Database\Ast\Node;
use NaN\Database\Sql\Query\Statements\{
	Interfaces\SqlStatementInterface,
	Traits\SqlStatementTrait,
};
use NaN\Database\Quotes;
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
			throw new \InvalidArgumentException('Insert statement must have at least one column!');
		}

		$this->_columns = $columns;

		return $this;
	}

	public function toAst(): Node {
		$ast = Ast::tree('insert', [
			Ast::raw('INSERT'),
			Ast::space(),
		]);

		if (isset($this->_target)) {
			$ast->push(Ast::clause([
				Ast::raw('INTO'),
				Ast::space(),
				$this->_target,
				Ast::space(),
			]));
		}

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
				Ast::value($this->_columns, Quotes::Auto, true),
			]));
		}

		if (isset($this->_where) && \count($this->_where)) {
			$ast->push(Ast::clause([
				Ast::raw('WHERE'),
				Ast::space(),
				$this->_where->toAst(),
				Ast::space(),
			]));
		}

		return $ast;
	}
}
