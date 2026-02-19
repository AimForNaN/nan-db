<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Sql\Query\Statements\Clauses\WhereClause;

trait WhereClauseTrait {
	protected WhereClause $_where;
	public function where(\Closure|string $column, ?string $operator = null, mixed $value = null): static {
		$this->_where = new WhereClause();

		if ($column instanceof \Closure) {
			$column($this->_where);
		} else {
			$this->_where->is($column, $operator, $value);
		}

		return $this;
	}
}
