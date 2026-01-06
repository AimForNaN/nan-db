<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Sql\Query\Statements\Clauses\WhereClause;

trait WhereClauseTrait {
	public function where(\Closure|string $column, ?string $operator = null, mixed $value = null): static {
		$where_clause = new WhereClause($this->_data);

		if ($column instanceof \Closure) {
			$column($where_clause);
		} else {
			$where_clause->is($column, $operator, $value);
		}

		return $this;
	}
}
