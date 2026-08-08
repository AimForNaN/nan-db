<?php

use NaN\Database\Connection;
use NaN\Database\Sql\Query\Builders\SqlQueryBuilder;
use NaN\Database\Sql\Query\Statements\Clauses\WhereClause;
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
		$db = Connection::connect([
			'dsn' => 'sqlite::memory:',
			'options' => [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
			],
		]);
		$query = new SqlQueryBuilder();
		$table = new TestTable();

		expect($table->create($db))->toBeTruthy();

		$result = $query
			->pull(['name'])
			->from('sqlite_master')
			->where(function (WhereClause $where) {
				$where->is('type', '=', 'table')
					  ->and('name', '=', 'test')
				;
			})
			->exec($db)
		;

		expect($result)->toBeInstanceOf(\PDOStatement::class)
			->and([...$result])->toHaveCount(1)
		;

		$result = $query
			->push([
				'id' => 255,
			])
			->into('test')
			->exec($db)
		;

		expect($result)->not()->toBeFalse();

		$results = $query
			->pull(['id'])
			->from('test')
			->exec($db)
		;

		expect($results)->toBeInstanceOf(\PDOStatement::class);

		$result = $results->fetch();
		expect($result['id'])->toBe(255);
	});
});
