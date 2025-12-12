<?php

namespace NaN\Database\Query\Statements\Traits;

trait ValuesClauseTrait {
	use StatementTrait;

	public function __construct(
		array $columns,
	) {
		$this->data = $columns;
	}

	public function count(): int {
		return \count($this->data);
	}

	static public function generatePlaceHolders(int $count): string {
		return \implode(', ', \array_fill(0, $count, '?'));
	}

	public function getBindings(): array {
		return \array_values($this->data);
	}

	static public function renderValue(mixed $value): string {
		switch (gettype($value)) {
			case 'string':
				return '"' . $value . '"';
		}

		return (string)$value;
	}

	public function validate(): bool {
		if (empty($this->data)) {
			return false;
		}

		return true;
	}
}