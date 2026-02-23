<?php

namespace NaN\Database\Sql\Traits;

use NaN\Database\Interfaces\ConnectionInterface;
use NaN\Database\Sql\Query\Renderers\SqlQueryRenderer;
use NaN\Database\Sql\Schema\SqlField;
use Nette\Schema\{
	Processor,
};
use NaN\Database\Ast;
use NaN\Database\Quotes;

trait SqlTableTrait {
	public static function create(ConnectionInterface $db): bool {
		$ast = Ast::tree('create', [
			Ast::raw('CREATE TABLE'),
			Ast::space(),
			Ast::identifier(static::NAME),
			Ast::space(),
		]);

		$fields = \iter\map(function (SqlField $field) {
			return $field->toAst();
		}, static::fields());
		$list = Ast::list(\iterator_to_array($fields));

		if (\count($list)) {
			$ast->push(Ast::group([$list]));
		}

		$query = new SqlQueryRenderer()->render($ast);
		var_dump($query);

		return (bool)$db->raw($query);
	}

	public static function drop(ConnectionInterface $db): bool {
		$ast = Ast::tree('drop', [
			Ast::raw('DROP TABLE'),
			Ast::space(),
			Ast::identifier(static::NAME),
		]);

		$query = new SqlQueryRenderer()->render($ast);

		return (bool)$db->raw($query);
	}

	public function primaryKey(): string {
		$fields = static::fields();
		$primary = \iter\search(fn($field) => $field->isPrimary(), $fields);
		return $primary->name;
	}

	public static function toValues(mixed $entity): array {
		$proc = new Processor();
		return $proc->process(static::fields(), $entity);
	}
}
