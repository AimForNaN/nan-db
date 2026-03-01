<?php

namespace NaN\Database\Ast;

use NaN\Database\Ast;

class Collection extends Tree {
	public function getIterator(): \Traversable {
		$delimiter = null;

		foreach ($this->_children as $idx => $child) {
			if ($delimiter) {
				yield Ast::raw($delimiter);
			}

			if ($child instanceof Tree) {
				yield from $child;
			} else {
				yield $child;
			}

			$delimiter = ', ';
		}
	}
}
