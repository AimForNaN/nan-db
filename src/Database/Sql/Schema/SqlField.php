<?php

namespace NaN\Database\Sql\Schema;

use NaN\Database\Ast;
use NaN\Database\Ast\Node;
use RavingDev\CaseConverter\CaseConverter;

/**
 * Represents a field schema.
 *
 * The methods below are handled using magic methods.
 *   They are technically suggestions, so their use is mostly for
 *   when you wish to have full compatibility with nan-db.
 *
 * Non-static method names are used for internal data keys, converted to snake_case.
 *   So, `autoIncrement` would be stored as `auto_increment`,
 *   `generatedAlwaysAsIdentity` would be stored as `generated_always_as_identity`, etc.
 *
 * @property string $name
 *
 * @method static self id(string $name = 'id') Instantiates field with type unsigned BIGINT and primary key constraint.
 * @method static self varchar(string $name) Instantiates field with type VARCHAR.
 *
 * @method self autoIncrement(bool $auto_increment = true) Marks field to be auto-incremented.
 * @method self default(mixed $value) Sets default value for field.
 * @method self max(int $max) Sets maximum possible value for column (useful for varchar, integers, etc).
 * @method self nullable(bool $nullable = true) Sets whether field can be nullable.
 * @method self primaryKey(bool $primary = true) Marks field as primary.
 * @method self uniqueKey(bool $unique = true) Marks field as unique (typically irrelevant for primary keys).
 * @method self unsigned(bool $unsigned = true) Marks field as unsigned.
 */
class SqlField extends Node implements Interfaces\SqlFieldInterface {
	public function __construct(public readonly string $name, string $type) {
		if (empty($name)) {
			throw new \InvalidArgumentException('Field name cannot be empty!');
		}

		if (empty($type)) {
			throw new \InvalidArgumentException('Field type cannot be empty!');
		}

		parent::__construct($type, [
			'name' => $name,
		]);
	}

	public function __call(string $name, array $args): static {
		$name = CaseConverter::toSnakeCase($name);
		[$value] = $args + [true];

		$this->__set($name, $value);

		return $this;
	}

	public static function __callStatic(string $type, array $args) {
		$type = CaseConverter::toUpperFlatCase($type);
		[$name] = $args + [null];

		switch ($type) {
			case 'ID': {
				$ret = new static($name ?? 'id', 'BIGINT')
					->primaryKey() //<< unique + not null
					->unsigned()
				;
				break;
			}
			default: {
				$ret = new static($name, $type);
			}
		}

		return $ret;
	}

	public function toAst(): Node {
		$ast = Ast::tree('field', [
			Ast::identifier($this->name),
			Ast::space(),
			Ast::raw($this->type),
		]);

		if ($this->max) {
			$ast->push(Ast::group([
				Ast::value($this->max),
			]));
		}

		foreach ($this->_data as $key => $value) {
//			switch ($key) {
//				case 'max':
//					continue 2;
//			}

			if ($value === true) {
				$split = CaseConverter::splitWords($key);
				$name = \strtoupper(\implode(' ', $split));
				$ast->push(Ast::space());
				$ast->push(Ast::raw($name));
			}
		}

		return $ast;
	}
}
