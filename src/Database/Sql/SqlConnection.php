<?php

namespace NaN\Database\Sql;

use NaN\Database\Interfaces\ConnectionInterface;

class SqlConnection implements ConnectionInterface {
	protected ?\PDO $_connection = null;

	/**
	 * @throws \PDOException|\Exception
	 */
	public function __construct(array $driver_config) {
		$this->_connection = new \PDO(
			$this->_generateDsn($driver_config),
			$driver_config['username'] ?? null,
			$driver_config['password'] ?? null,
			$driver_config['options'] ?? null,
		);
	}

	public function __call(string $name, array $args) {
		return \call_user_func_array([$this->_connection, $name], $args);
	}

	public function __get(string $name) {
		return $this->_connection->$name;
	}

	public function close(): bool {
		$this->_connection = null;
		return true;
	}

	/**
	 * @throws \PDOException|\Exception
	 */
	public function exec(mixed $query, array $bindings = []): \PDOStatement|false {
		return $this->raw($query, $bindings);
	}

	public function getLastInsertId(): string | false {
		return $this->_connection->lastInsertId();
	}

	public function raw(string $query, array $bindings = []): \PDOStatement|false {
		$db = $this->_connection;

		if (empty($bindings)) {
			return $db->query($query);
		}

		$stmt = $db->prepare($query);

		if ($stmt instanceof \PDOStatement) {
			if (!$stmt->execute($bindings)) {
				return false;
			}

			return $stmt;
		}

		return false;
	}

	/**
	 * @param array $driver_config
	 *
	 * @return string
	 *
	 * @throws \Exception
	 */
	protected function _generateDsn(array $driver_config): string {
		if (empty($driver_config['driver'])) {
			throw new \Exception('Driver not specified!');
		}

		$prefix = $driver_config['driver'];
		$config = $driver_config[$prefix] ?? null;

		if (empty($config)) {
			throw new \Exception('Driver configuration not provided!');
		}

		if (\is_array($config)) {
			$config = \array_map(fn($key, $value) => "{$key}={$value}", \array_keys($config), \array_values($config));
			return "{$prefix}:" . \implode(';', $config);
		}

		return "{$prefix}:{$config}";
	}
}
