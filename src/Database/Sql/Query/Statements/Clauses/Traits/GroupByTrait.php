<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

trait GroupByTrait {
	protected array $_group_by = [];

	public function groupBy(array $columns): static {
		if (empty($columns)) {
			throw new \InvalidArgumentException('Group-by columns must not be empty!');
		}

		$this->_group_by = $columns;

		return $this;
	}
}
