<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Ast;
use NaN\Database\Quotes;

trait OrderByTrait {
	protected array $_order_by = [];

	public function orderBy(array $order_by): static {
		if (empty($order_by)) {
			throw new \InvalidArgumentException('Order-by columns must not be empty!');
		}

		$this->_order_by = $order_by;

		return $this;
	}
}
