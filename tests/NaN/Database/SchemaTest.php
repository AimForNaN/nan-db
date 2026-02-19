<?php

use NaN\Database\Sql\Schema\{SqlField};

describe('SQL Schema', function () {
	test('SQL field case conversion', function () {
		$field = SqlField::bigint('id');

		expect($field->type)->toBe('BIGINT');
	});
});
