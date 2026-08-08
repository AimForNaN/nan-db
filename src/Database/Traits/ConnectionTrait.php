<?php

namespace NaN\Database\Traits;

use NaN\Database\Interfaces\ConnectionInterface;

trait ConnectionTrait {
	public function __construct(
		protected ?\PDO $_connection,
	) {
		if (\is_null($this->_connection)) {
			throw new \ValueError('PDO connection required!');
		}
	}

	public function __call(string $name, array $args) {
		return \call_user_func_array([$this->_connection, $name], $args);
	}

	public function __get(string $name) {
		return $this->_connection->$name;
	}

	public function close(): void {
		$this->_connection = null;
	}

	static public function connect(array $config): ConnectionInterface {
		$config['dsn'] = self::generateDsn($config['dsn'] ?? []);

		return new self(\PDO::connect(...$config));
	}

	/**
	 * @param array|string $dsn
	 *
	 * @return string
	 *
	 * @throws \ValueError if `$dsn` is empty!
	 */
	static public function generateDsn(array|string $dsn): string {
		if (empty($dsn)) {
			throw new \ValueError('DSN is required!');
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

	public function getPdo(): \PDO {
		return $this->_connection;
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
}
