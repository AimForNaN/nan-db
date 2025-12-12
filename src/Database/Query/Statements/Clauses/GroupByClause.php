<?php

namespace NaN\Database\Query\Statements\Clauses;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;
use NaN\Database\Query\Statements\Traits\StatementTrait;

final class GroupByClause implements StatementInterface {
	use StatementTrait;

	public function __construct(array $columns = []) {
		$this->data = $columns;
	}

	public function getBindings(): array {
		return [];
	}

	public function render(bool $prepared = false): string {
		return 'GROUP BY ' . \implode(', ', $this->data);
	}

	public function validate(): bool {
		if (empty($this->data)) {
			return false;
		}

		return true;
	}
}
