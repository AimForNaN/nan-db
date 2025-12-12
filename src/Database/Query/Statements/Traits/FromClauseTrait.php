<?php

namespace NaN\Database\Query\Statements\Traits;

use NaN\Database\Query\Statements\Clauses\FromClause;
use NaN\Database\Query\Statements\Pull;

trait FromClauseTrait {
	public function from(\Closure|string $table, string $database = ''): static {
		if (empty($table)) {
			\trigger_error('From clause: Table name cannot be empty!', E_USER_ERROR);
		}

		$from_clause = new FromClause();

		if ($table instanceof \Closure) {
			$sub_query = new Pull();
			$table($sub_query);
			$from_clause->addSubQuery($sub_query, $database);
		} else {
			$from_clause->addTable($table, $database);
		}

		return $this->setFrom($from_clause);
	}
}
