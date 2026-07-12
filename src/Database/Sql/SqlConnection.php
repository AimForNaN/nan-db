<?php

namespace NaN\Database\Sql;

use NaN\Database\Interfaces\ConnectionInterface;
use NaN\Database\Query\Builders\Interfaces\QueryBuilderInterface;
use NaN\Database\Query\Renderers\Interfaces\RendererInterface;
use NaN\Database\Query\Statements\Interfaces\StatementInterface;
use NaN\Database\Sql\Query\{
	Builders\SqlQueryBuilder,
	Renderers\SqlQueryRenderer,
};

class SqlConnection implements ConnectionInterface {
	protected ?\PDO $_connection = null;
	protected RendererInterface $_renderer;

	/**
	 * @throws \PDOException|\RuntimeException
	 */
	public function __construct(\PDO $connection) {
		$this->_connection = $connection;
		$this->_renderer = new SqlQueryRenderer();
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

	public function queryBuilder(): QueryBuilderInterface {
		return new SqlQueryBuilder();
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
