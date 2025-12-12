<?php

namespace NaN\Database\Query\Statements;

use NaN\Database\Query\Statements\Clauses\{
	FromClause,
	GroupByClause,
	LimitClause,
	OrderByClause,
	SelectClause,
	WhereClause,
};
use NaN\Database\Query\Statements\Traits\{
	FromClauseTrait,
	GroupByTrait,
	LimitClauseTrait,
	OrderByTrait,
	WhereClauseTrait,
};
use NaN\Database\Query\Statements\Traits\StatementTrait;

class Pull implements Interfaces\PullInterface {
	use FromClauseTrait;
	use GroupByTrait;
	use LimitClauseTrait;
	use OrderByTrait;
	use StatementTrait;
	use WhereClauseTrait;

	public function __construct(array $columns = []) {
		$this->setSelect(new SelectClause());
	}

	public function __invoke(...$args): static {
		return $this->pull(...$args);
	}

	public function first(): static {
		$this->limit();
		return $this;
	}

	public function last(string $column): static {
		$this->orderBy([$column => 'desc']);
		$this->limit();
		return $this;
	}

	public function pull(array $columns, bool $distinct = false): static {
		$select_clause = new SelectClause();
		$select_clause->setColumns($columns);

		if ($distinct) {
			$select_clause->distinct();
		}

		return $this->setSelect($select_clause);
	}

	protected function setFrom(FromClause $from_clause): static {
		$this->data[1] = $from_clause;
		return $this;
	}

	protected function setGroupBy(GroupByClause $group_by_clause): static {
		$this->data[3] = $group_by_clause;
		return $this;
	}

	protected function setLimit(LimitClause $limit_clause): static {
		$this->data[5] = $limit_clause;
		return $this;
	}

	protected function setOrderBy(OrderByClause $order_by_clause): static {
		$this->data[4] = $order_by_clause;
		return $this;
	}

	protected function setSelect(SelectClause $select_clause): static {
		$this->data[0] = $select_clause;
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

		if (!\is_a($this->data[0], SelectClause::class)) {
			return false;
		}

		if (empty($this->data[1])) {
			return false;
		}

		if (!\is_a($this->data[1], FromClause::class)) {
			return false;
		}

		return true;
	}
}
