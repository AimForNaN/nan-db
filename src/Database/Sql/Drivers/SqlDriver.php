<?php

namespace NaN\Database\Sql\Drivers;

use NaN\Database\Drivers\Interfaces\DriverInterface;
use NaN\Database\Interfaces\ConnectionInterface;
use NaN\Database\Query\Builders\Interfaces\QueryBuilderInterface;
use NaN\Database\Sql\Query\{
	Builders\SqlQueryBuilder,
	Renderers\SqlQueryRenderer,
};
use NaN\Database\Sql\SqlConnection;

class SqlDriver implements DriverInterface {
	/**
	 * @param array $driver_config
	 *
	 * @return ConnectionInterface
	 *
	 * @throws \Exception
	 */
	public function createConnection(
		array $driver_config = [],
	): ConnectionInterface {
		return new SqlConnection($driver_config, new SqlQueryRenderer());
	}

	public function createQueryBuilder(): QueryBuilderInterface {
		return new SqlQueryBuilder();
	}
}
