<?php

use NaN\Database\Sql\Query\Renderers\SqlQueryRenderer;
use NaN\Database\Sql\Schema\SqlField;

describe('SQL Schema', function () {
	test('SQL field case conversion', function () {
		$field = SqlField::id('id');
		$renderer = new SqlQueryRenderer();

		expect($renderer->render($field->toAst()))->toBe('`id` BIGINT PRIMARY KEY AUTOINCREMENT UNSIGNED');
	});
});
