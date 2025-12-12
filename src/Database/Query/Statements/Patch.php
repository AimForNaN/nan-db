<?php

namespace NaN\Database\Query\Statements;

use NaN\Database\Query\Statements\Clauses\{
	LimitClause,
	UpdateClause,
	UpdateValuesClause,
	WhereClause,
};
use NaN\Database\Query\Statements\Traits\{
	LimitClauseTrait,
	StatementTrait,
	WhereClauseTrait,
};

class Patch implements Interfaces\PatchInterface {
	use LimitClauseTrait;
	use StatementTrait;
	use WhereClauseTrait;

	public function __invoke(...$args): static {
		return $this->patch(...$args);
	}

	public function patch(string $table, string $database = ''): static {
		return $this->setUpdate(new UpdateClause($table, $database));
	}

	protected function setLimit(LimitClause $limit_clause): static {
		$this->data[3] = $limit_clause;
		return $this;
	}

	protected function setUpdate(UpdateClause $update_clause): static {
		$this->data[0] = $update_clause;
		return $this;
	}

	protected function setValues(UpdateValuesClause $insert_values_clause): static {
		$this->data[1] = $insert_values_clause;
		return $this;
	}

	protected function setWhere(WhereClause $where_clause): static {
		$this->data[2] = $where_clause;
		return $this;
	}

	public function validate(): bool {
		if (\count($this->data) === 0) {
			return false;
		}

		if (empty($this->data[0])) {
			return false;
		}

		if (!\is_a($this->data[0], UpdateClause::class)) {
			return false;
		}

		if (empty($this->data[2])) {
			return false;
		}

		if (!\is_a($this->data[2], WhereClause::class)) {
			return false;
		}

		return true;
	}

	public function with(array $columns): static {
		return $this->setValues(new UpdateValuesClause($columns));
	}
}

