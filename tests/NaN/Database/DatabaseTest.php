<?php

use NaN\Database\Drivers\SqlDriver;
use NaN\Database\Query\Statements\Interfaces\{
	PullInterface,
	PushInterface,
};

describe('Database', function () {
	test('Push and pull', function () {
		$driver = new SqlDriver([
			'driver' => 'sqlite',
			'sqlite' => ':memory:',
		]);
		$db = $driver->createConnection();

		expect($db->raw('CREATE TABLE `test` (`id` int);'))->toBeTruthy();

		$result = $db->raw('SELECT `name` FROM `sqlite_master` WHERE type="table" AND name="test";');
		expect($result)->toBeInstanceOf(\PDOStatement::class);
		expect([...$result])->toHaveCount(1);

		$db->push(function (PushInterface $push) {
			$push->push([
				'id' => 255,
			])->into('test');
		});
		$results = $db->pull(function (PullInterface $pull) {
			$pull->pull([
				'id',
			])->from('test');
		});

		expect($results)->toBeInstanceOf(\PDOStatement::class);

		$result = $results->fetch();
		expect($result['id'])->toBe(255);
	});
});
