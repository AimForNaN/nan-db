<?php

namespace NaN\Database\Sql\Schema\Traits;

use NaN\Database\Ast;
use NaN\Database\Interfaces\ConnectionInterface;
use NaN\Database\Sql\Query\Renderers\SqlQueryRenderer;
use NaN\Database\Sql\Schema\SqlField;

trait SqlTableTrait {
	public function create(ConnectionInterface $db): \PDOStatement|bool {
		$ast = Ast::tree('create', [
			Ast::raw('CREATE TABLE'),
			Ast::space(),
			Ast::identifier(static::NAME),
			Ast::space(),
		]);

		$fields = \iter\map(function (SqlField $field) {
			return $field->toAst();
		}, $this->fields());
		$list = Ast::list(\iterator_to_array($fields));

		if (\count($list)) {
			$ast->push(Ast::group([$list]));
		}

		$query = new SqlQueryRenderer()->render($ast);

		return $db->raw($query);
	}

	public function drop(ConnectionInterface $db): \PDOStatement|bool {
		$ast = Ast::tree('drop', [
			Ast::raw('DROP TABLE'),
			Ast::space(),
			Ast::identifier(static::NAME),
		]);

		$query = new SqlQueryRenderer()->render($ast);

		return $db->raw($query);
	}

	protected function toValue(mixed $value): mixed {
		return match (gettype($value)) {
			'array' => \json_encode($value),
			'boolean' => \filter_var($value, FILTER_VALIDATE_BOOLEAN),
			'double' => \filter_var($value, FILTER_VALIDATE_FLOAT),
			'integer' => \filter_var($value, FILTER_VALIDATE_INT),
			default => (string)$value,
		};
	}

	public function toValues(object $entity): array {
		$ret = [];

		foreach ($this->fields() as $field) {
			$ret[$field->name] = $this->toValue($entity->{$field->name});
		}

		return $ret;
	}
}
