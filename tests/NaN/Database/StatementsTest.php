<?php

use NaN\Database\Sql\Query\{
	Renderers\SqlQueryRenderer,
	Statements\DeleteStatement,
	Statements\InsertStatement,
	Statements\SelectStatement,
	Statements\UpdateStatement,
};

describe('Statements', function () {
	test('Patch', function () {
		$query = new UpdateStatement(new SqlQueryRenderer());

		$columns = [
			'id' => 255,
			'name' => 'test',
		];
		$query->update('test')
			->with($columns)
		;
		expect((string)$query)->toBe('UPDATE `test` SET `id` = ?, `name` = ?')
			->and($query->getBindings())->toBe(\array_values($columns))
		;
	});

	test('Pull', function () {
		$query = new SelectStatement(new SqlQueryRenderer());

		$query->select()
			->from('test')
		;
		expect((string)$query)->toBe('SELECT ALL FROM `test`');

		$query->select(['id'])
			->from('test')
			->where('id', '=', 255)
			->groupBy(['id', 'test'])
			->orderBy([
				'id' => 'desc',
				'name' => 'asc',
			])
			->limit(1, 1)
		;
		expect((string)$query)->toBe(\implode(' ', [
			'SELECT `id` FROM `test`',
			'WHERE `id` = ?',
			'GROUP BY `id`, `test`',
			'ORDER BY `id` DESC, `name` ASC',
			'LIMIT 1',
			'OFFSET 1',
			]))
			->and($query->getBindings())->toBe([255])
		;
	});

	test('Purge', function () {
		$query = new DeleteStatement(new SqlQueryRenderer());

		$query->from('test');
		expect((string)$query)->toBe('DELETE FROM `test`');
	});

	test('Push', function () {
		$query = new InsertStatement(new SqlQueryRenderer());

		$columns = ['id' => 255, 'name' => 'test'];
		$query->insert($columns)
			->into('test')
		;
		expect((string)$query)->toBe('INSERT INTO `test` (`id`, `name`) VALUES (?, ?)')
			->and($query->getBindings())->toBe(\array_values($columns))
		;
	});
});
