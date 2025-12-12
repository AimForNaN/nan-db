<?php

namespace NaN\Database\Query\Statements\Interfaces;

interface PatchInterface extends LimitClauseInterface, StatementInterface, WhereClauseInterface {
	public function patch(string $table, string $database = ''): static;

	public function with(array $columns): static;
}

