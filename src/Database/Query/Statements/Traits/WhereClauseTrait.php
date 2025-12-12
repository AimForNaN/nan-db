<?php

namespace NaN\Database\Query\Statements\Traits;

use NaN\Database\Query\Statements\Clauses\WhereClause;

trait WhereClauseTrait {
	public function where(\Closure|string $column, string $operator = '', mixed $value = ''): static {
		$where_clause = new WhereClause();

		if ($column instanceof \Closure) {
			$column($where_clause);
		} else {
			$where_clause($column, $operator, $value);
		}

		return $this->setWhere($where_clause);
	}
}
