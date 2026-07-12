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
		$driver_config['dsn'] = $this->_generateDsn($driver_config['dsn'] ?? []);

		return new SqlConnection(\PDO::connect(...$driver_config));
	}

	/**
	 * @param array|string $dsn
	 *
	 * @return string
	 *
	 * @throws \RuntimeException if `$dsn` is empty!
	 */
	protected function _generateDsn(array|string $dsn): string {
		if (empty($dsn)) {
			throw new \RuntimeException('DSN is required!');
		}

		if (\is_string($dsn)) {
			return $dsn;
		}

		$prefix = $dsn['prefix'];

		unset($dsn['prefix']);

		$config = \array_map(fn($key, $value) => "{$key}={$value}", \array_keys($dsn), \array_values($dsn));
		$config = \implode(';', $config);

		return "{$prefix}:{$config}";
	}
}
