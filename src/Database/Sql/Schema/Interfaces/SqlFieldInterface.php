<?php

namespace NaN\Database\Sql\Schema\Interfaces;

use NaN\Database\Ast\Node;

interface SqlFieldInterface {
	public string $name {
		get;
	}

	public function isPrimary(): bool;

	public function toAst(): Node;
}
