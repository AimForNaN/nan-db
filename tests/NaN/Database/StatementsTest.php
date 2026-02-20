<?php

use NaN\Database\Sql\Query\{
	Renderers\SqlQueryRenderer,
	Statements\Clauses\WhereClause,
	Statements\DeleteStatement,
	Statements\InsertStatement,
	Statements\SelectStatement,
	Statements\UpdateStatement,
};

describe('Statements', function () {
	test('Patch', function () {
		$renderer = new SqlQueryRenderer();
		$query = new UpdateStatement();

		$columns = [
			'id' => 255,
			'name' => 'test',
		];
		$query->update('test')
			->with($columns)
		;
		expect($renderer->render($query->toAst()))->toBe('UPDATE `test` SET `id` = ?, `name` = ?')
			->and($query->getBindings())->toBe(\array_values($columns))
		;
	});

	test('Pull', function () {
		$renderer = new SqlQueryRenderer();
		$query = new SelectStatement();

		$query->select()
			->from('test')
		;
		expect($renderer->render($query->toAst()))->toBe('SELECT ALL FROM `test`');

		$query->select(['id'])
			->from('test')
			->where(function (WhereClause $where) {
				$where->prepare()->is('id', '=', 255);
			})
			->groupBy(['id', 'test'])
			->orderBy([
				'id' => 'desc',
				'name' => 'asc',
			])
			->limit(1, 1)
		;
		expect($renderer->render($query->toAst()))->toBe(\implode(' ', [
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
		$renderer = new SqlQueryRenderer();
		$query = new DeleteStatement();

		$query->from('test');
		expect($renderer->render($query->toAst()))->toBe('DELETE FROM `test`');
	});

	test('Push', function () {
		$renderer = new SqlQueryRenderer();
		$query = new InsertStatement();

		$columns = ['id' => 255, 'name' => 'test'];
		$query->insert($columns)
			->into('test')
		;
		expect($renderer->render($query->toAst()))->toBe('INSERT INTO `test` (`id`, `name`) VALUES (?, ?)')
			->and($query->getBindings())->toBe(\array_values($columns))
		;
	});
});
