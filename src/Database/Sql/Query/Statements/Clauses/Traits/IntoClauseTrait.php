<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Ast;

trait IntoClauseTrait {
	use TableRefTrait;

	public function into(string $table, string $database = ''): static {
		$into = $this->_createTableReference('into', $table, $database);

		$this->_data->unshift($into);

		return $this;
	}
}
