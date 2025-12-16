<?php

namespace NaN\Database\Interfaces;

use NaN\Database\Query\Statements\{
	Interfaces\PatchInterface,
	Interfaces\PullInterface,
	Interfaces\PurgeInterface,
	Interfaces\PushInterface,
};
use Nette\Schema\Schema;

interface TableInterface {
	public const string DATABASE_NAME = '';
	public const string TABLE_NAME = '';

	public static function patch(): PatchInterface;

	public static function pull(): PullInterface;

	public static function purge(): PurgeInterface;

	public static function push(): PushInterface;

	public static function schema(): Schema;

	public static function toValues(mixed $entity): array;
}
