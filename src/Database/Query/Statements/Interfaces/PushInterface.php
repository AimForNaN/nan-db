<?php

namespace NaN\Database\Query\Statements\Interfaces;

interface PushInterface extends StatementInterface, WhereClauseInterface {
	public function into(string $table, string $database = ''): static;

	public function push(array $columns): static;
}
