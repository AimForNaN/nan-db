<?php

namespace NaN\Database\Query\Statements\Clauses;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;
use NaN\Database\Query\Statements\Traits\StatementTrait;

final class FromClause implements StatementInterface {
	use StatementTrait;

	public function addSubQuery(StatementInterface $query, string $alias = ''): static {
		$this->data[] = [
			'expr' => 'query',
			'query' => $query,
			'alias' => $alias,
		];
		return $this;
	}

	public function addTable(string $table, string $database = '', string $alias = ''): static {
		$this->data[] = [
			'expr' => 'table',
			'alias' => $alias,
			'database' => $database,
			'table' => $table,
		];
		return $this;
	}

	public function getBindings(): array {
		return \array_reduce($this->data, function ($ret, $item) {
			/**
			 * @var array $bindings
			 * @var StatementInterface $query
			 */
			\extract($item);

			switch ($expr) {
				case 'query':
					return \array_merge($ret, $query->getBindings());
			}

			return $ret;
		}, []);
	}

	public function render(bool $prepared = false): string {
		return 'FROM ' . \implode(', ', \array_filter(\array_map(function ($column) use ($prepared) {
			/**
			 * @var string $alias
			 * @var string $database
			 * @var \NaN\Database\Query\Statements\Interfaces\StatementInterface $query
			 * @var string $table
			 */
			\extract($column);

			$ret = '';

			switch ($expr) {
				case 'query':
					$ret .= '(' . $query->render($prepared) . ')';

					if (!empty($alias)) {
						$ret .= 'AS ' . $alias;
					}

					break;
				case 'table':
					if (!empty($database)) {
						$ret .= $database . '.';
					}

					$ret .= $table;

					if (!empty($alias)) {
						$ret .= 'AS ' . $alias;
					}

					break;
			}

			return $ret;
		}, $this->data)));
	}
}
