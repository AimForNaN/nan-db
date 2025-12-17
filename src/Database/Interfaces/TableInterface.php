<?php

namespace NaN\Database\Interfaces;

use Nette\Schema\Schema;

interface TableInterface {
	public const string DATABASE_NAME = '';
	public const string TABLE_NAME = '';

	public static function render(): string;

	public static function schema(): Schema;

	public static function toValues(mixed $entity): array;
}
