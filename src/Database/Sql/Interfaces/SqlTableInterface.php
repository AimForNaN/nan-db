<?php

namespace NaN\Database\Sql\Interfaces;

use Nette\Schema\Schema;

interface SqlTableInterface {
	public const DATABASE_NAME = '';
	public const TABLE_NAME = '';

	public static function render(): string;

	public static function schema(): Schema;

	public static function toValues(mixed $entity): array;
}
