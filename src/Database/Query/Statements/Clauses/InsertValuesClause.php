<?php

namespace NaN\Database\Query\Statements\Clauses;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;
use NaN\Database\Query\Statements\Traits\ValuesClauseTrait;

final class InsertValuesClause implements StatementInterface, \Countable {
	use ValuesClauseTrait;

	public function render(bool $prepared = false): string {
		$columns = [];
		$values = [];

		foreach ($this->data as $column => $value) {
			$columns[] = $column;
			$values[] = $value;
		}

		return '(' . \implode(',', $columns) . ') VALUES (' . static::renderValues($values, $prepared) . ')';
	}

	static public function renderValues(array $values, bool $prepared = false): string {
		$args = [];

		foreach ($values as $value) {
			$args[] = $prepared ? '?' : static::renderValue($value);
		}

		return \implode(', ', $args);
	}

	public function toUpdateValues(): UpdateValuesClause {
		return new UpdateValuesClause($this->data);
	}
}
