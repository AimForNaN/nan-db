<?php

namespace NaN\Database\Drivers;

use NaN\Database\Drivers\Interfaces\DriverInterface;
use NaN\Database\Query\Builders\Interfaces\QueryBuilderInterface;
use NaN\Database\Query\Builders\SqlQueryBuilder;

class SqlDriver implements DriverInterface {
	public function __construct(
		protected array $config = [],
	) {
	}

	/**
	 * @throws \Exception
	 */
	public function createConnection(array $options = []): QueryBuilderInterface {
		return new SqlQueryBuilder(
			new \PDO(
				$this->generateDsn(),
				$this->config['username'] ?? null,
				$this->config['password'] ?? null,
				$this->config['options'] ?? null,
			),
			$options,
		);
	}

	/**
	 * @throws \Exception
	 */
	public function generateDsn(): string {
		if (empty($this->config['driver'])) {
			throw new \Exception('Driver not configured!');
		}

		$prefix = $this->config['driver'];
		$config = $this->config[$prefix] ?? null;

		if (empty($config)) {
			throw new \Exception('Driver not configured!');
		}

		if (\is_array($config)) {
			$config = \array_map(fn($key, $value) => "{$key}={$value}", \array_keys($config), \array_values($config));
			return "{$prefix}:" . \implode(';', $config);
		}

		return "{$prefix}:{$config}";
	}
}
