<?php

namespace NaN\Database\Interfaces;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;

interface ConnectionInterface {
	public function close(): bool;
	public function exec(mixed $query): mixed;
	public function raw(string $query): mixed;
}
