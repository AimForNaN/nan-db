<?php

namespace NaN\Database\Query\Statements\Clauses;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;
use NaN\Database\Query\Statements\Traits\StatementTrait;

final class DeleteClause implements StatementInterface {
	use StatementTrait;

	public function getBindings(): array {
		return [];
	}

	public function render(bool $prepared = false): string {
		return 'DELETE';
	}

	public function validate(): bool {
		return true;
	}
}
