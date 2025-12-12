<?php

namespace NaN\Database\Query\Statements\Clauses;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;
use NaN\Database\Query\Statements\Traits\ValuesClauseTrait;

final class UpdateValuesClause implements \Countable, StatementInterface {
	use ValuesClauseTrait;

	public function render(bool $prepared = false): string {
		return 'SET ' . static::renderValues($this->data, $prepared);
	}

	static public function renderValues(iterable $values, bool $prepared = false): string {
		$args = [];

		foreach ($values as $column => $value) {
			$args[] = "$column = " . ($prepared ? '?' : static::renderValue($value));
		}

		return \implode(', ', $args);
	}
}
