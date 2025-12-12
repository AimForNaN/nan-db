<?php

namespace NaN\Database\Query\Statements\Interfaces;

interface FromClauseInterface {
	public function from(\Closure|string $table, string $database = ''): static;
}
