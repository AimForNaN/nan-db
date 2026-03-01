<?php

namespace NaN\Database\Sql\Query\Statements\Interfaces;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;

interface SqlStatementInterface extends StatementInterface {
	public function getBindings(): array;
}
