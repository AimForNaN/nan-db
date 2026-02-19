<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Ast\Tree;

trait IntoClauseTrait {
	use TableRefTrait;

	protected Tree $_target;

	public function into(string $table, string $database = ''): static {
		$this->_target = $this->_createTableReference($table, $database);

		return $this;
	}
}
