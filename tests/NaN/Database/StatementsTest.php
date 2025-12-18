<?php

use NaN\Database\Query\Statements\{
    Patch,
    Pull,
    Purge,
    Push,
};

describe('Statements', function () {
	test('Patch', function () {
		$patch = new Patch();

		$patch->patch('test')->with(['id' => 255]);
		expect($patch->render())->toBe('UPDATE test SET id = 255');
		expect($patch->render(true))->toBe('UPDATE test SET id = ?');
	});

	test('Pull', function () {
		$pull = new Pull();

		$pull->from('test')->where('id', '=', 1);
		expect($pull->render())->toBe('SELECT * FROM test WHERE id = 1');

		$pull = new Pull();
		$pull->pull(['id'])->from('test');
		expect($pull->render())->toBe('SELECT id FROM test');

		$pull = new Pull();
		$pull->from(function ($pull) {
			expect($pull)->toBeInstanceOf(Pull::class);
			$pull->from('test');
		});
		expect($pull->render())->toBe('SELECT * FROM (SELECT * FROM test)');
	});

	test('Purge', function () {
		$purge = new Purge();

		$purge->from('test');
		expect($purge->render())->toBe('DELETE FROM test');
	});

	test('Push', function () {
		$push = new Push();

		$push->push(['id' => 255])->into('test');
		expect($push->render())->toBe('INSERT INTO test (id) VALUES (255)');
		expect($push->render(true))->toBe('INSERT INTO test (id) VALUES (?)');
		expect($push->getBindings())->toBe([255]);
	});
});
