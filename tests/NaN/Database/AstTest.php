<?php

use NaN\Database\Ast;

describe('Abstract syntax tree', function () {
	it('is a node', function () {
		$node = ['type' => 'node'];

		expect(Ast::is($node))->toBeTrue()
			->and(Ast::is($node, 'node'))->toBeTrue()
			->and(Ast::is($node, $node))->toBeTrue()
			->and(Ast::is($node, [['type' => 'expr'], 'node']))->toBeTrue()
			->and(Ast::is(['name' => 'node', 'type' => 'node'], ['name' => 'node']))->toBeTrue()
		;
	});

	it('is not a node', function () {
		expect(Ast::is([]))->toBeFalse()
			->and(Ast::is(['type' => 'node'], 'expr'))->toBeFalse()
		;
	});

	it('push', function () {
		$parent = Ast::node('parent');
		$child = Ast::node('child');

		ASt::push($child, $parent);

		$expr = Ast::expr('test');

		Ast::push($expr, $child);

		expect($parent)->toBe(Ast::node('parent', [
			Ast::node('child', [
				Ast::expr('test'),
			]),
		]));
	});

//	it('visit', function () {});
});
