<?php

namespace NaN\Database\Sql\Query\Statements\Interfaces;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;
use NaN\Database\Sql\Prepare;

interface SqlStatementInterface extends StatementInterface {
	public function getBindings(): array;

	public function prepare(Prepare $prepare): SqlStatementInterface;
}
