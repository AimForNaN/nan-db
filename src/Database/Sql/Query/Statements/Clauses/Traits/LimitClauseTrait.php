<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Ast;

trait LimitClauseTrait {
	protected int $_offset = 0;
	protected int $_limit = 0;

	public function limit(int $limit = 1, int $offset = 0): static {
		if ($limit < 1) {
			throw new \InvalidArgumentException('Limit must be greater than 0!');
		}

		if ($offset !== 0) {
			if ($offset < 1) {
				throw new \InvalidArgumentException('Offset must be greater than 0!');
			}
		}

		$this->_limit = $limit;
		$this->_offset = $offset;

		return $this;
	}
}
