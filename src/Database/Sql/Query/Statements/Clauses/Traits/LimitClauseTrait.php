<?php

namespace NaN\Database\Sql\Query\Statements\Clauses\Traits;

use NaN\Database\Ast;

trait LimitClauseTrait {
	public function limit(int $limit = 1, int $offset = 0): static {
		if ($limit < 1) {
			throw new \InvalidArgumentException('Limit must be greater than 0!');
		}

		$limit = Ast::tree('limit', [
			Ast::expr(value: $limit),
		]);

		$this->_data->push($limit);

		if ($offset !== 0) {
			if ($offset < 1) {
				throw new \InvalidArgumentException('Offset must be greater than 0!');
			}

			$offset = Ast::tree('offset', [
				Ast::expr(value: $offset),
			]);

			$this->_data->push($offset);
		}

		return $this;
	}
}
