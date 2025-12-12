<?php

namespace NaN\Database\Query\Statements\Clauses;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;
use NaN\Database\Query\Statements\Traits\StatementTrait;

final class InsertClause implements StatementInterface {
	use StatementTrait;

	public function getBindings(): array {
		return [];
	}

	public function render(bool $prepared = false): string {
		return 'INSERT';
	}

	public function validate(): bool {
		return true;
	}
}
