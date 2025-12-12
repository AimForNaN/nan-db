<?php

namespace NaN\Database\Query\Statements\Traits;

use NaN\Database\Query\Statements\Clauses\LimitClause;

trait LimitClauseTrait {
	public function limit(int $limit = 1, int $offset = 0): static {
		$limit_clause = new LimitClause($limit, $offset);
		return $this->setLimit($limit_clause);
	}
}
