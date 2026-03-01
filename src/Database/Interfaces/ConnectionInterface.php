<?php

namespace NaN\Database\Interfaces;

interface ConnectionInterface {
	public function close(): bool;

	public function getPdo(): \PDO;

	public function raw(string $query): mixed;
}
