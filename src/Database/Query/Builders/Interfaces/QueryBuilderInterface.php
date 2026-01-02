<?php

namespace NaN\Database\Query\Builders\Interfaces;

use NaN\Database\Query\Statements\Interfaces\StatementInterface;

interface QueryBuilderInterface {
	public function patch(): StatementInterface;

	public function pull(): StatementInterface;

	public function purge(): StatementInterface;

	public function push(): StatementInterface;
}
