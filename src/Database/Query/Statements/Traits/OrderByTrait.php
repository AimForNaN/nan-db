<?php

namespace NaN\Database\Query\Statements\Traits;

use NaN\Database\Query\Statements\Clauses\OrderByClause;

trait OrderByTrait {
	public function orderBy(array $order): static {
		$order_by_clause = new OrderByClause($order);
		return $this->setOrderBy($order_by_clause);
	}
}
