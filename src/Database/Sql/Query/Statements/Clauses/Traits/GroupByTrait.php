<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Ast;
use NaN\Database\Quotes;

trait GroupByTrait {
	public function groupBy(array $columns): static {
		if (empty($columns)) {
			throw new \InvalidArgumentException('Columns must not be empty!');
		}

		$group_by = Ast::tree('group by');
		$group_by_list = Ast::list(\array_map(fn($column) => Ast::expr($column, quotes: [Quotes::Backtick]), $columns));

		$group_by->push($group_by_list);
		$this->_data->push($group_by);

		return $this;
	}
}
