<?php

use NaN\Database\Ast;
use NaN\Database\Query\Statements\Clauses\{
    GroupByClause,
    LimitClause,
    OrderByClause,
};
use NaN\Database\Sql\Query\Renderers\SqlQueryRenderer;
use NaN\Database\Sql\Query\Statements\Clauses\WhereClause;

describe('Clauses', function () {
	test('GroupBy clause', function () {
		$group_by = new GroupByClause(['id', 'test']);
		expect($group_by->render())->toBe('GROUP BY id, test');
	});

	test('Limit clause', function () {
		$limit = new LimitClause();
		expect($limit->render())->toBe('LIMIT 1');

		$limit = new LimitClause(1, 255);
		expect($limit->render())->toBe('LIMIT 1, 255');
	});

	test('OrderBy clause', function () {
		$order_by = new OrderByClause([
			'id' => 'asc',
			'test' => 'desc',
		]);
		expect($order_by->render())->toBe('ORDER BY id asc, test desc');
	});

	test('Where clause', function () {
		$node = Ast::node('node');
		$query = new WhereClause($node);
		$renderer = new SqlQueryRenderer();

		$query->is('test', '=', 255)
			->and('test', 'IN', [255])
			->or(function (WhereClause $query) {
				$query->is('test', '>', 255);
			})
		;

		expect($renderer->render($node['children'][0]))->toBe('WHERE test = ? AND test IN (?) OR (test > ?)')
//			->and($renderer->getBindings())->toBe([255, 255, 255])
		;
	});
});
