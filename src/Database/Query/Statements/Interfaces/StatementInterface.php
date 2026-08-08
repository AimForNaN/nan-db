<?php

namespace NaN\Database\Query\Statements\Interfaces;

use NaN\Database\Interfaces\ConnectionInterface;

interface StatementInterface extends ClauseInterface {
	public function exec(ConnectionInterface $connection): mixed;
}
