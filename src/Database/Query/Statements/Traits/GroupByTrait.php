<?php

namespace NaN\Database\Query\Statements\Traits;

use NaN\Database\Query\Statements\Clauses\GroupByClause;

trait GroupByTrait {
	public function groupBy(array $columns): static {
		$group_by_clause = new GroupByClause($columns);
		return $this->setGroupBy($group_by_clause);
	}
}
