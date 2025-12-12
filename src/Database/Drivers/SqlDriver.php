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

	public function generateDsn(): string {
		if (empty($this->config['driver'])) {
			\trigger_error('Driver not configured!', E_USER_ERROR);
		}

		$prefix = $this->config['driver'];
		$config = $this->config[$prefix] ?? null;

		if (empty($config)) {
			\trigger_error('Driver not configured!', E_USER_ERROR);
		}

		if (\is_array($config)) {
			$config = \array_map(fn($key, $value) => "{$key}={$value}", \array_keys($config), \array_values($config),);
			return "{$prefix}:" . \implode(';', $config);
		}

		return "{$prefix}:{$config}";
	}
}