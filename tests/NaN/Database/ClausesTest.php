<?php

use NaN\Database\Query\Statements\Clauses\{
    GroupByClause,
    LimitClause,
    OrderByClause,
    WhereClause,
};

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
		$where = new WhereClause();

		$where('test', '=', 255)
			->and('test', 'IN', [255])
			->or(function (WhereClause $where) {
				$where('test', '>', 255);
			})
		;

		expect($where->render())->toBe('WHERE test = 255 AND test IN (255) OR (test > 255)');
		expect($where->render(true))->toBe('WHERE test = ? AND test IN (?) OR (test > ?)');
		expect($where->getBindings())->toBe([255, 255, 255]);
	});
});
