<?php

namespace NaN\Database\Sql;

use NaN\Database\Interfaces\ConnectionInterface;
use NaN\Database\Query\Renderers\Interfaces\RendererInterface;
use NaN\Database\Query\Statements\Interfaces\StatementInterface;
use NaN\Database\Sql\Query\Renderers\SqlQueryRenderer;

class SqlConnection implements ConnectionInterface {
	protected ?\PDO $_connection = null;
	protected RendererInterface $_renderer;

	/**
	 * @throws \PDOException|\Exception
	 */
	public function __construct(
		array $driver_config,
	) {
		$this->_renderer = new SqlQueryRenderer();

		$driver_config['dsn'] = $this->_generateDsn($driver_config['dsn'] ?? []);

		$this->_connection = \PDO::connect(...$driver_config);
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
	 * @param StatementInterface $query
	 *
	 * @return \PDOStatement|false
	 */
	public function exec(StatementInterface $query): \PDOStatement|false {
		$sql = $this->_renderer->render($query->toAst());
		return $this->raw($sql, $query->getBindings());
	}

	public function getLastInsertId(): string | false {
		return $this->_connection->lastInsertId();
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

	/**
	 * @param array $driver_config
	 *
	 * @return string
	 *
	 * @throws \Exception
	 */
	protected function _generateDsn(array|string $dsn): string {
		if (empty($dsn)) {
			throw new \Exception('DSN is required!');
		}

		if (\is_string($dsn)) {
			return $dsn;
		}

		$prefix = $dsn[0];

		unset($dsn[0]);

		$config = \array_map(fn($key, $value) => "{$key}={$value}", \array_keys($config), \array_values($config));
		$config = \implode(';', $config);

		return "{$prefix}:{$config}";
	}
}
