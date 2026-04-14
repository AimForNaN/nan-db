<?php

use NaN\Database\Sql\Drivers\SqlDriver;
use NaN\Database\Sql\Query\{
	Statements\Clauses\WhereClause,
	Statements\InsertStatement,
	Statements\SelectStatement,
};
use NaN\Database\Sql\Schema\{
	Interfaces\SqlTableInterface,
	SqlField,
	Traits\SqlTableTrait,
};

class TestTable implements SqlTableInterface {
	use SqlTableTrait;

	const string NAME = 'test';

	public function fields(): \Generator {
		yield SqlField::int('id');
	}

	public static function indices(): \Generator {
		yield;
	}
}

describe('Database', function () {
	test('Push and pull', function () {
		$driver = new SqlDriver();
		$db = $driver->createConnection([
			'dsn' => 'sqlite::memory:',
			'options' => [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
			],
		]);
		$query = $driver->createQueryBuilder();
		$table = new TestTable();

		expect($table->create($db))->toBeTruthy();

		$result = $db->exec(
			$query
				->pull(['name'])
				->from('sqlite_master')
				->where(function (WhereClause $where) {
					$where->is('type', '=', 'table')
						  ->and('name', '=', 'test')
					;
				})
		);
		expect($result)->toBeInstanceOf(\PDOStatement::class)
			->and([...$result])->toHaveCount(1)
		;

		$result = $db->exec(
			$query
				->push([
					'id' => 255,
				])
				->into('test')
		);

		expect($result)->not()->toBeFalse();

		$results = $db->exec(
			$query
				->pull(['id'])
				->from('test')
		);

		expect($results)->toBeInstanceOf(\PDOStatement::class);

		$result = $results->fetch();
		expect($result['id'])->toBe(255);
	});
});
