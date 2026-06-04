<?php

use NaN\Database\Sql\Query\Raw;
use NaN\Database\Sql\Query\Renderers\SqlQueryRenderer;
use NaN\Database\Sql\Query\Statements\Clauses\WhereClause;

describe('Clauses', function () {
	test('Where clause', function () {
		$query = new WhereClause();
		$renderer = new SqlQueryRenderer();

		$query->prepare()->is('test', '=', 255)
			->and('test', 'IN', [255])
			->and('test', 'NOT IN', [0])
			->and('test', 'IS NOT', null)
			->and('test', '>', new Raw('CURRENT_TIMESTAMP()'))
			->or(function (WhereClause $query) {
				$query->is('test', '>', 255);
			})
		;

		expect($renderer->render($query->toAst()))->toBe(\implode(' ', [
			'`test` = ?',
			'AND `test` IN (?)',
			'AND `test` NOT IN (?)',
			'AND `test` IS NOT NULL',
			'AND `test` > CURRENT_TIMESTAMP()',
			'OR (`test` > ?)',
		]));

		$query = new WhereClause();

		$query->is('test', '=', 'isn\'t')
		      ->and('test', 'IN', [255])
		      ->and('test', 'NOT IN', [0])
		      ->and('test', 'IS NOT', null)
		      ->and('test', '>', new Raw('CURRENT_TIMESTAMP()'))
		      ->or(function (WhereClause $query) {
				  $query->is('test', '>', 255);
			  })
		;

		expect($renderer->render($query->toAst()))->toBe(\implode(' ', [
			'`test` = \'isn&apos;t\'',
			'AND `test` IN (255)',
			'AND `test` NOT IN (0)',
			'AND `test` IS NOT NULL',
			'AND `test` > CURRENT_TIMESTAMP()',
			'OR (`test` > 255)',
		]));
	});
});
