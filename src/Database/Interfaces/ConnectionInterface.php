<?php

namespace NaN\Database\Interfaces;

interface ConnectionInterface {
	public function close(): bool;
	public function exec(mixed $query): mixed;
}
