<?php

namespace NaN\Database\Interfaces;

interface ConnectionInterface {
	public function close(): void;

	static public function connect(array $config): ConnectionInterface;

	public function raw(string $query): mixed;
}
