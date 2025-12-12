<?php

namespace NaN\Database\Query\Statements;

use NaN\Database\Query\Statements\Clauses\{
	InsertClause,
	InsertIntoClause,
	InsertValuesClause,
	WhereClause,
};
use NaN\Database\Query\Statements\Traits\{
	StatementTrait,
	WhereClauseTrait,
};

class Push implements Interfaces\PushInterface {
	use StatementTrait;
	use WhereClauseTrait;

	public function __construct() {
		$this->setInsert(new InsertClause());
	}

	public function __invoke(...$args): static {
		return $this->push(...$args);
	}

	public function into(string $table, string $database = ''): static {
		return $this->setInsertInto(new InsertIntoClause($table, $database));
	}

	public function push(array $columns): static {
		return $this->setInsertValues(new InsertValuesClause($columns));
	}

	protected function setInsert(InsertClause $insert_clause): static {
		$this->data[0] = $insert_clause;
		return $this;
	}

	protected function setInsertInto(InsertIntoClause $into): static {
		$this->data[1] = $into;
		return $this;
	}

	protected function setInsertValues(InsertValuesClause $values): static {
		$this->data[2] = $values;
		return $this;
	}

	protected function setWhere(WhereClause $where_clause): static {
		$this->data[3] = $where_clause;
		return $this;
	}

	public function validate(): bool {
		if (\count($this->data) === 0) {
			return false;
		}

		if (empty($this->data[0])) {
			return false;
		}

		if (!\is_a($this->data[0], InsertClause::class)) {
			return false;
		}

		if (empty($this->data[1])) {
			return false;
		}

		if (!\is_a($this->data[1], InsertIntoClause::class)) {
			return false;
		}

		if (empty($this->data[2])) {
			return false;
		}

		if (!\is_a($this->data[2], InsertValuesClause::class)) {
			return \count($this->data[2]);
		}

		return true;
	}
}
