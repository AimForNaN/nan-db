<?php

namespace NaN\Database\Query\Statements\Clauses;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;
use NaN\Database\Query\Statements\Traits\StatementTrait;

final class UpdateClause implements StatementInterface {
	use StatementTrait;

	public function __construct(
		string $table,
		string $database = '',
	) {
		$this->data['table'] = $table;
		$this->data['database'] = $database;
	}

	public function getBindings(): array {
		return [];
	}

	public function render(bool $prepared = false): string {
		$table = $this->data['table'];

		if (!empty($this->data['database'])) {
			$table .= '.' . $this->data['database'];
		}

		return 'UPDATE ' . $table;
	}

	public function validate(): bool {
		if (empty($this->data['table'])) {
			return false;
		}

		return true;
	}
}
