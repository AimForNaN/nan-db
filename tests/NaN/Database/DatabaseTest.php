<?php

use NaN\Database\Drivers\SqlDriver;
use NaN\Database\Query\Statements\Raw;
use NaN\Database\Query\Statements\Interfaces\{
	PullInterface,
	PushInterface,
};

describe('Database', function () {
	test('Push and pull', function () {
		$driver = new SqlDriver();
		$db = $driver->createConnection([
			'driver' => 'sqlite',
			'sqlite' => ':memory:',
		]);
		$query = $driver->createQueryBuilder();

		expect($db->exec(new Raw('CREATE TABLE `test` (`id` int);')))->toBeTruthy();

		$result = $db->exec(new Raw('SELECT `name` FROM `sqlite_master` WHERE type="table" AND name="test";'));
		expect($result)->toBeInstanceOf(\PDOStatement::class)
			->and([...$result])->toHaveCount(1)
		;

		$query->push(function (PushInterface $query) {
			$query->push([
				'id' => 255,
			])->into('test');
		})->exec($db);
		$results = $query->pull(function (PullInterface $query) {
			$query->pull([
				'id',
			])->from('test');
		})->exec($db);

		expect($results)->toBeInstanceOf(\PDOStatement::class);

		$result = $results->fetch();
		expect($result['id'])->toBe(255);
	});
});
