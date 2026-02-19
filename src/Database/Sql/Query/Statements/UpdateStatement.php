<?php

namespace NaN\Database\Sql\Query\Statements;

use NaN\Database\Ast;
use NaN\Database\Ast\{Node,Tree};
use NaN\Database\Sql\Query\Statements\{
	Interfaces\SqlStatementInterface,
	Traits\SqlStatementTrait,
};
use NaN\Database\Sql\Query\Statements\Clauses\{
	Traits\FromClauseTrait,
	Traits\LimitClauseTrait,
	Traits\OrderByTrait,
	Traits\TableRefTrait,
	Traits\WhereClauseTrait,
};

class UpdateStatement implements SqlStatementInterface {
	use SqlStatementTrait;
	use FromClauseTrait;
	use LimitClauseTrait;
	use OrderByTrait;
	use TableRefTrait;
	use WhereClauseTrait;

	protected array $_entries = [];
	protected Tree $_target;

	public function toAst(): Node {
		$ast = Ast::tree('update', [
			Ast::raw('UPDATE'),
			Ast::space(),
		]);

		if (\count($this->_target)) {
			$ast->push($this->_target);
			$ast->push(Ast::space());
		}

		$list = Ast::list();

		foreach ($this->_entries as $column => $value) {
			$column = Ast::match($column, '=', $value);

			$list->push($column);
		}

		if (\count($list)) {
			$ast->push(Ast::raw('SET'));
			$ast->push(Ast::space());
			$ast->push($list);
		}

		return $ast;
	}

	public function update(string $table, ?string $database = null): static {
		$this->_target = $this->_createTableReference($table, $database);

		return $this;
	}

	public function with(array $columns): static {
		$this->_entries = $columns;

		return $this;
	}
}

