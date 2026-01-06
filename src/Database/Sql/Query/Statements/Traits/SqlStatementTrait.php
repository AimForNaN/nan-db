<?php

namespace NaN\Database\Sql\Query\Statements\Traits;

use NaN\Database\Ast;
use NaN\Database\Ast\Node;
use NaN\Database\Interfaces\ConnectionInterface;
use NaN\Database\Query\Statements\Traits\StatementTrait;

trait SqlStatementTrait {
	use StatementTrait;

	public function exec(ConnectionInterface $connection): mixed {
		return $connection->exec($this->__toString(), $this->getBindings());
	}

	public function getBindings(): array {
		$bindings = [];

		if ($this->_data->raw) {
			return $bindings;
		}

		Ast::visit($this->_data, ['where', 'set', 'values'], function ($node) use (&$bindings) {
			Ast::visit($node, 'expression', function (Node $expr) use (&$bindings) {
				$parts = $expr->getData('parts');
				[, , $value] = $parts;

				if (\is_array($value)) {
					\array_push($bindings, ...$value);
				} else {
					$bindings[] = $value;
				}
			});
		});

		return $bindings;
	}

	public function raw(): self {
		$this->_data->raw = true;

		return $this;
	}
}
