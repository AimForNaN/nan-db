<?php

namespace NaN\Database\Sql;

use NaN\Database\Interfaces\ConnectionInterface;
use NaN\Database\Query\Renderers\Interfaces\RendererInterface;
use NaN\Database\Sql\Query\Statements\Interfaces\SqlStatementInterface;

class SqlConnection implements ConnectionInterface {
	protected ?\PDO $_connection = null;

	/**
	 * @throws \PDOException|\Exception
	 */
	public function __construct(
		array $driver_config,
		protected RendererInterface $_renderer,
	) {
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
	 * @param SqlStatementInterface $query
	 *
	 * @throws \PDOException|\Exception
	 */
	public function exec(mixed $query): \PDOStatement|false {
		if (!\is_subclass_of($query, SqlStatementInterface::class)) {
			throw new \InvalidArgumentException('An instance of SqlStatementInterface required!');
		}

		$sql = $this->_renderer->render($query->toAst());
		return $this->raw($sql, $query->getBindings());
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
