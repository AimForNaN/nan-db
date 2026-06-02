<?php

namespace NaN\Database\Sql\Drivers;

use NaN\Database\Drivers\Interfaces\DriverInterface;
use NaN\Database\Interfaces\ConnectionInterface;
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
		return new SqlConnection($driver_config);
	}
}
