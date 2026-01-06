<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Ast;
use NaN\Database\Quotes;

trait OrderByTrait {
	public function orderBy(array $order): static {
		$order_by = Ast::tree('order by');
		$order_by_list = Ast::list();

		foreach ($order as $column => $direction) {
			$expr = Ast::expr($column, \strtoupper($direction), quotes: [Quotes::Backtick]);

			$order_by_list->push($expr);
		}

		$order_by->push($order_by_list);
		$this->_data->push($order_by);

		return $this;
	}
}
