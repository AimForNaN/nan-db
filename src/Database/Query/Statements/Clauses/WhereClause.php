<?php

namespace NaN\Database\Query\Statements\Clauses;

use NaN\Database\Query\Statements\{
	Interfaces\StatementInterface,
	Traits\StatementTrait,
};

final class WhereClause implements StatementInterface {
	use StatementTrait;

	public function __invoke(string $column, string $operator, mixed $value): self {
		$this->_addColumn(null, $column, $operator, $value);
		return $this;
	}

	/**
	 * Add where expression.
	 *
	 * @param ?string $delimiter AND, OR... Use null for first where expression.
	 * @param string $column
	 * @param string $operator =, >=, <=, IN...
	 * @param mixed $value
	 *
	 * @return static
	 */
	protected function _addColumn(?string $delimiter, string $column, string $operator, mixed $value): self {
		$this->data[$delimiter ?: \count($this->data)] = [
			'expr' => 'condition',
			'delimiter' => $delimiter,
			'column' => $column,
			'operator' => $operator,
			'value' => $value,
		];
		return $this;
	}

	/**
	 * Add sub where clause.
	 *
	 * @param ?string $delimiter AND, OR...
	 * @param \Closure $fn
	 *
	 * @return static
	 */
	protected function _addGroup(?string $delimiter, \Closure $fn): self {
		$where_group = new static();
		$this->data[$delimiter ?: \count($this->data)] = [
			'expr' => 'group',
			'delimiter' => $delimiter,
			'value' => $where_group,
		];

		$fn($where_group);

		return $this;
	}

	/**
	 * Add AND where expression.
	 *
	 * @param \Closure|string $column
	 * @param string|null $operator =, >=, <=, IN...
	 * @param mixed $value
	 *
	 * @return static
	 *
	 * @see _addColumn()
	 */
	public function and(\Closure|string $column, ?string $operator = null, mixed $value = null): self {
		if ($column instanceof \Closure) {
			return $this->_addGroup('AND', $column);
		}

		return $this->_addColumn('AND', $column, $operator, $value);
	}

	public function getBindings(): array {
		return \array_reduce($this->data, function ($ret, $item) {
			/**
			 * @var string $expr
			 * @var mixed $value
			 */
			\extract($item);

			switch ($expr) {
				case 'condition':
					if (\is_array($value)) {
						return \array_merge($ret, $value);
					}

					$ret[] = $value;
					break;
				case 'group':
					return \array_merge($ret, $value->getBindings());
			}

			return $ret;
		}, []);
	}

	/**
	 * Add OR where expression.
	 *
	 * @param \Closure|string $column
	 * @param string|null $operator =, >=, <=, IN...
	 * @param mixed $value
	 *
	 * @return static
	 *
	 * @see _addColumn()
	 */
	public function or(\Closure|string $column, ?string $operator = null, mixed $value = null): self {
		if ($column instanceof \Closure) {
			return $this->_addGroup('OR', $column);
		}

		return $this->_addColumn('OR', $column, $operator, $value);
	}

	public function render(bool $prepared = false): string {
		return 'WHERE ' . \array_reduce($this->data, function ($ret, $item) use ($prepared) {
			/**
			 * @var string $condition
			 * @var string $delimiter
			 * @var string $expr
			 * @var mixed $value
			 */
			\extract($item);

			if ($delimiter) {
				$ret .= ' ' . \strtoupper($delimiter) . ' ';
			}

			switch ($expr) {
				case 'condition':
					$ret .= "{$column} {$operator} " . static::renderValue($value, $prepared);
					break;
				case 'group':
					$ret .= '(' . \str_replace('WHERE ', '', $value->render($prepared)) . ')';
					break;
			}

			return $ret;
		}, '');
	}

	static public function renderValue(mixed $value, bool $prepared = false): string {
		switch (gettype($value)) {
			case 'array':
				return '(' . \implode(', ', \array_map(fn($v) => self::renderValue($v, $prepared), $value)) . ')';
			case 'string':
				return $prepared ? '?' : '"' . $value . '"';
		}

		return $prepared ? '?' : (string)$value;
	}

	public function validate(): bool {
		if (empty($this->data)) {
			return false;
		}

		foreach ($this->data as $item) {
			switch ($item['expr']) {
				case 'condition':
					if (empty($item['condition'])) {
						return false;
					}

					if (empty($item['operator'])) {
						return false;
					}
					break;
				case 'group':
					if (!$item['value']->validate()) {
						return false;
					}
					break;
			}
		}

		return true;
	}
}
