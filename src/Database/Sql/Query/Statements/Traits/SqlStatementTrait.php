<?php

namespace NaN\Database\Sql\Query\Statements\Traits;

use NaN\Database\Sql\Prepare;
use NaN\Database\Sql\Query\Statements\Interfaces\SqlStatementInterface;

trait SqlStatementTrait {
	protected Prepare $_prepare = Prepare::All;

	public function getBindings(): array {
		$ast = $this->toAst();
		$bindings = [];

		foreach ($ast as $node) {
			if ($node->type === 'value' && $node->prepare) {
				$bindings[] = $node->value;
			}
		}

		return $bindings;
	}

	public function prepare(Prepare $prepare): SqlStatementInterface {
		$this->_prepare = $prepare;

		return $this;
	}
}
