<?php

namespace NaN\Database\Sql\Query;

class Raw implements \Stringable {
	public function __construct(
		private string $__sql,
	) {
	}

	public function __toString(): string {
		return $this->__sql;
	}
}
