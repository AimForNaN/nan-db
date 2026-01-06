<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Ast;
use NaN\Database\Quotes;

trait FromClauseTrait {
	use TableRefTrait;

	public function from(string $table, ?string $database = null): static {
		$from = $this->_createTableReference('from', $table, $database);

		$this->_data->push($from);

		return $this;
	}
}
