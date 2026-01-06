<?php

namespace NaN\Database\Traits;

use Nette\Schema\{
	Expect,
	Processor,
	Schema,
};

trait SqlTableTrait {
	public static function render(): string {
		return '';
	}

	public static function schema(): Schema {
		return Expect::structure([]);
	}

	public static function toValues(mixed $entity): array {
		$proc = new Processor();
		return $proc->process(static::schema(), $entity);
	}
}
