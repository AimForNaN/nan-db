<?php

namespace NaN\Database\Traits;

use NaN\Database\Query\Statements\{
	Interfaces\PatchInterface,
	Interfaces\PullInterface,
	Interfaces\PurgeInterface,
	Interfaces\PushInterface,
	Patch,
	Pull,
	Purge,
	Push,
};
use Nette\Schema\{
	Expect,
	Processor,
	Schema,
};

trait TableTrait {
	public static function patch(): PatchInterface {
		$patch = new Patch();

		$patch->patch(static::TABLE_NAME, static::DATABASE_NAME);

		return $patch;
	}

	public static function pull(): PullInterface {
		$pull = new Pull();

		$pull->pull(['*'])->from(static::TABLE_NAME, static::DATABASE_NAME);

		return $pull;
	}

	public static function purge(): PurgeInterface {
		$purge = new Purge();

		$purge->from(static::TABLE_NAME, static::DATABASE_NAME);

		return $purge;
	}

	public static function push(): PushInterface {
		$push = new Push();

		$push->into(static::TABLE_NAME, static::DATABASE_NAME);

		return $push;
	}

	public static function schema(): Schema {
		return Expect::structure([]);
	}

	public static function toValues(mixed $entity): array {
		$proc = new Processor();
		return $proc->process(static::schema(), $entity);
	}
}
