<?php

namespace NaN\Database\Sql\Converters;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use NaN\Database\Ast\Node;
use NaN\Database\Sql\Schema\Interfaces\SqlTableInterface;

class TableConverter {
	public static array $laravel_blueprint = [
		'BIGINT' => 'bigInteger',
		'BOOLEAN' => 'boolean',
		'CHAR' => 'char',
		'DATETIME' => 'dateTime',
		'DATE' => 'date',
		'DOUBLE' => 'double',
		'FLOAT' => 'float',
		'INT' => 'integer',
		'INTEGER' => 'integer',
		'MEDIUMINT' => 'mediumInteger',
		'SMALLINT' => 'smallInteger',
		'TINYINT' => 'tinyInteger',
		'MEDIUMTEXT' => 'mediumText',
		'TEXT' => 'text',
		'TIMESTAMP' => 'timestamp',
		'VARCHAR' => 'string',
		'auto_increment' => 'autoIncrement',
		'default' => 'default',
		'primary_key' => 'primaryKey',
		'index' => 'index',
		'nullable' => 'nullable',
		'unique_key' => 'unique',
		'unsigned' => 'unsigned',
	];

	public function processLaravelBlueprint(SqlTableInterface $table, Blueprint $blueprint): void {
		/** @var Node $field */
		foreach ($table->fields() as $field) {
			/** @var ColumnDefinition $column */
			$column = match ($field->type) {
				'VARCHAR' => $blueprint->string($field->name, $field->max),
				default => $blueprint->{self::$laravel_blueprint[$field->type]}($field->name),
			};

			foreach ($field->getData() as $key => $value) {
				$column->{self::$laravel_blueprint[$key]}($value);
			}
		}
	}
}
