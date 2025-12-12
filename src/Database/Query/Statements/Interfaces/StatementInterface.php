<?php

namespace NaN\Database\Query\Statements\Interfaces;

interface StatementInterface {
	public function getBindings(): array;

	public function render(bool $prepared = false): string;

	public function validate(): bool;
}
