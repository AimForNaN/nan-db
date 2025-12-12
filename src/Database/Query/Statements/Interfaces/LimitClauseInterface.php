<?php

namespace NaN\Database\Query\Statements\Interfaces;

interface LimitClauseInterface {
	public function limit(int $limit = 1, int $offset = 0): static;
}
