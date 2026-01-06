<?php

use NaN\Database\Sql\Drivers\SqlDriver;
use NaN\Database\Sql\Query\{
	Statements\InsertStatement,
	Statements\Raw,
	Statements\SelectStatement,
};

describe('Database', function () {
	test('Push and pull', function () {
		$driver = new SqlDriver();
		$db = $driver->createConnection([
			'driver' => 'sqlite',
			'sqlite' => ':memory:',
			'options' => [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
			],
		]);
		$query = $driver->createQueryBuilder();

		expect(new Raw('CREATE TABLE `test` (`id` int);')->exec($db))->toBeTruthy();

		$result = new Raw('SELECT `name` FROM `sqlite_master` WHERE type="table" AND name="test";')->exec($db);
		expect($result)->toBeInstanceOf(\PDOStatement::class)
			->and([...$result])->toHaveCount(1)
		;

		$result = $query->push(function (InsertStatement $query) {
			$query->insert([
				'id' => 255,
			])->into('test');
		})->exec($db);

		expect($result)->not()->toBeFalse();

		$results = $query->pull(function (SelectStatement $query) {
			$query->select([
				'id',
			])->from('test');
		})->exec($db);

		expect($results)->toBeInstanceOf(\PDOStatement::class);

		$result = $results->fetch();
		expect($result['id'])->toBe(255);
	});//->depends('Pull', 'Push');
});
