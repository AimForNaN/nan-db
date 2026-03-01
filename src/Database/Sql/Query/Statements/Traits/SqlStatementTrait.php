<?php

namespace NaN\Database\Sql\Query\Statements\Traits;

trait SqlStatementTrait {
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
}
