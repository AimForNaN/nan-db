<?php

namespace NaN\Database\Sql\Query\Statements\Interfaces;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;

interface SqlStatementInterface extends StatementInterface {
	public function getBindings(): array;

	/**
	 * Mark statement as raw.
	 *
	 * Used to disable automatically generating prepared statements.
	 *
	 * @return self
	 */
	public function raw(): self;
}
