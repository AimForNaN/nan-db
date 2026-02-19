<?php

use NaN\Database\Ast;
use NaN\Database\Sql\Query\Renderers\SqlQueryRenderer;
use NaN\Database\Sql\Query\Statements\Clauses\WhereClause;

describe('Clauses', function () {
	test('Where clause', function () {
		$query = new WhereClause();
		$renderer = new SqlQueryRenderer();

		$query->is('test', '=', 255)
			->and('test', 'IN', [255])
			->or(function (WhereClause $query) {
				$query->is('test', '>', 255);
			})
		;

		expect($renderer->render($query))->toBe('`test` = ? AND `test` IN (?) OR (`test` > ?)');
	});
});
