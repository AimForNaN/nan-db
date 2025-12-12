<?php

namespace NaN\Database\Query\Statements;

use NaN\Database\Query\Statements\Clauses\{
	DeleteClause,
	FromClause,
	LimitClause,
	OrderByClause,
	WhereClause,
};
use NaN\Database\Query\Statements\Traits\{
	FromClauseTrait,
	LimitClauseTrait,
	OrderByTrait,
	StatementTrait,
	WhereClauseTrait,
};

class Purge implements Interfaces\PurgeInterface {
	use FromClauseTrait;
	use LimitClauseTrait;
	use OrderByTrait;
	use StatementTrait;
	use WhereClauseTrait;

	public function __construct() {
		$this->setDelete(new DeleteClause());
	}

	protected function setDelete(DeleteClause $delete_clause): static {
		$this->data[0] = $delete_clause;
		return $this;
	}

	protected function setFrom(FromClause $from_clause): static {
		$this->data[1] = $from_clause;
		return $this;
	}

	protected function setLimit(LimitClause $limit_clause): static {
		$this->data[4] = $limit_clause;
		return $this;
	}

	protected function setOrderBy(OrderByClause $order_by_clause): static {
		$this->data[3] = $order_by_clause;
		return $this;
	}

	protected function setWhere(WhereClause $where_clause): static {
		$this->data[2] = $where_clause;
		return $this;
	}

	public function validate(): bool {
		if (count($this->data) == 0) {
			return false;
		}

		if (empty($this->data[0])) {
			return false;
		}

		if (!\is_a($this->data[0], DeleteClause::class)) {
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
}
