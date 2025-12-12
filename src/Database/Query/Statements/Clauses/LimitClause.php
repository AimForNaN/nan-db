<?php

namespace NaN\Database\Query\Statements\Clauses;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;
use NaN\Database\Query\Statements\Traits\StatementTrait;

final class LimitClause implements StatementInterface {
	use StatementTrait;

	public function __construct(
		int $limit = 1,
		int $offset = 0,
	) {
		$this->data['limit'] = $limit;
		$this->data['offset'] = $offset;
	}

	public function getBindings(): array {
		return [];
	}

	public function render(bool $prepared = false): string {
		$ret = 'LIMIT ' . $this->data['limit'];

		if (!empty($this->data['offset'])) {
			$ret .= ', ' . $this->data['offset'];
		}

		return $ret;
	}

	public function validate(): bool {
		if (!isset($this->data['limit'])) {
			return false;
		}

		return true;
	}
}
