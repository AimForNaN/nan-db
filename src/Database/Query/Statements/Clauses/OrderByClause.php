<?php

namespace NaN\Database\Query\Statements\Clauses;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;
use NaN\Database\Query\Statements\Traits\StatementTrait;

final class OrderByClause implements StatementInterface {
	use StatementTrait;

	public function __construct(array $columns) {
		$this->data = $columns;
	}

	public function getBindings(): array {
		return [];
	}

	public function render(bool $prepared = false): string {
		$columns = [];

		foreach ($this->data as $column => $order) {
			$columns[] = "$column $order";
		}

		return 'ORDER BY ' . \implode(', ', $columns);
	}

	public function validate(): bool {
		if (empty($this->data)) {
			return false;
		}

		return true;
	}
}
