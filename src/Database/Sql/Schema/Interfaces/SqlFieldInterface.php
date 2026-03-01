<?php

namespace NaN\Database\Sql\Schema\Interfaces;

use NaN\Database\Ast\Node;

interface SqlFieldInterface {
	public string $name {
		get;
	}
	public string $type {
		get;
	}

	public function toAst(): Node;
}
