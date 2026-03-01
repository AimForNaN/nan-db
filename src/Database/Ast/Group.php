<?php

namespace NaN\Database\Ast;

use NaN\Database\Ast;

class Group extends Tree {
	public function getIterator(): \Traversable {
		yield Ast::raw('(');
		yield from parent::getIterator();
		yield Ast::raw(')');
	}
}
