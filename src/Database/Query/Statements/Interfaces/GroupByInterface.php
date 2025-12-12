<?php

namespace NaN\Database\Query\Statements\Interfaces;

interface GroupByInterface {
	public function groupBy(array $columns): static;
}
