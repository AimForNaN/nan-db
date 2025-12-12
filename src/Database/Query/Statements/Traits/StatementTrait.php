<?php

namespace NaN\Database\Query\Statements\Traits;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;

trait StatementTrait {
	protected array $data = [];

	public function getBindings(): array {
		return \array_reduce($this->data, function (array $ret, StatementInterface $stmt): array {
			return \array_merge($ret, $stmt->getBindings());
		}, []);
	}

	public function render(bool $prepared = false): string {
		ksort($this->data);
		return \implode(' ',
			\array_map(fn(StatementInterface $stmt) => $stmt->render($prepared), $this->data)
		);
	}

	public function validate(): bool {
		if (empty($this->data)) {
			return false;
		}

		foreach ($this->data as $clause) {
			if ($clause instanceof StatementInterface) {
				if (!$clause->validate()) {
					return false;
				}
			}
		}

		return true;
	}
}
