<?php

namespace NaN\Database\Sql\Query\Statements\Traits;

use NaN\Database\Interfaces\ConnectionInterface;
use NaN\Database\Sql\Query\Renderers\SqlQueryRenderer;

trait SqlStatementTrait {
	public function exec(ConnectionInterface $connection): mixed {
		$renderer = new SqlQueryRenderer();
		$query = $renderer->render($this->toAst());
		return $connection->exec($query, $this->getBindings());
	}

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
