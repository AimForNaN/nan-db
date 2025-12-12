<?php

namespace NaN\Database\Query\Statements\Interfaces;

interface PullInterface extends FromClauseInterface, GroupByInterface, LimitClauseInterface, OrderByInterface, StatementInterface, WhereClauseInterface {
	public function first(): static;

	public function last(string $column): static;

	public function pull(array $columns, bool $distinct = false): static;
}
