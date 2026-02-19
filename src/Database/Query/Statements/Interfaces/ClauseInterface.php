<?php

namespace NaN\Database\Query\Statements\Interfaces;

use NaN\Database\Ast\Node;

interface ClauseInterface {
	public function toAst(): Node;
}
