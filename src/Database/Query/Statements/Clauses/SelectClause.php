<?php

namespace NaN\Database\Query\Statements\Clauses;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;
use NaN\Database\Query\Statements\Traits\StatementTrait;

final class SelectClause implements StatementInterface {
	use StatementTrait;

	public function __construct(
		array $columns = ['*'],
		private bool $distinct = false,
	) {
		$this->setColumns($columns);
	}

	public function __invoke(array $columns) {
		$this->setColumns($columns);
	}

	protected function addColumn(string $column, string $alias = ''): static {
		$this->data[] = [
			'expr' => 'column',
			'alias' => $alias,
			'column' => $column,
		];

		return $this;
	}

	public function setColumns(array $columns): static {
		$this->data = [];

		foreach ($columns as $alias => $column) {
			if (!\is_numeric($alias)) {
				$this->addColumn($column, $alias);
			} else {
				$this->addColumn($column);
			}
		}

		return $this;
	}

	public function distinct(): static {
		$this->distinct = true;
		return $this;
	}

	public function getBindings(): array {
		return [];
	}

	public function render(bool $prepared = false): string {
		return 'SELECT ' . ($this->distinct ? 'DISTINCT ' : '') . \implode(', ', \array_map(function ($item) {
			/**
			 * @var string $expr
			 * @var string $column
			 * @var string $alias
			 */
			\extract($item);

			switch ($expr) {
				case 'column':
					if (!empty($alias)) {
						return "$column AS $alias";
					}
			}

			return $column;
		}, $this->data));
	}

	public function validate(): bool {
		if (empty($this->data)) {
			return false;
		}

		foreach ($this->data as $item) {
			switch ($item['expr']) {
				case 'column':
					if (empty($item['column'])) {
						return false;
					}
					break;
			}
		}

		return true;
	}
}
