<?php

namespace NaN\Database\Sql\Schema\Interfaces;

interface SqlTableInterface {
	public const string NAME = '';

	public function fields(): \Generator;

	public function toValues(object $entity): array;
}
