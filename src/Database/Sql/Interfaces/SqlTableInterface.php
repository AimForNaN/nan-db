<?php

namespace NaN\Database\Sql\Interfaces;

use NaN\Database\Interfaces\ConnectionInterface;

interface SqlTableInterface {
	public const string NAME = '';

	public static function create(ConnectionInterface $db): bool;

	public static function drop(ConnectionInterface $db): bool;

	public static function fields(): \Generator;

	public function primaryKey(): string;

	public static function toValues(mixed $entity): array;
}
