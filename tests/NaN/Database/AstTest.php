<?php

use NaN\Database\Ast;
use NaN\Database\Ast\Node;

describe('Abstract syntax tree', function () {
	it('is a node', function () {
		$node = new Node('node', [
			'name' => 'node',
		]);

		expect(Ast::is($node))->toBeTrue()
			->and(Ast::is($node, 'node'))->toBeTrue()
			->and(Ast::is($node, [['type' => 'expr'], 'node']))->toBeTrue()
			->and(Ast::is($node, ['name' => 'node']))->toBeTrue()
		;
	});

	it('is not a node', function () {
		expect(Ast::is([]))->toBeFalse()
			->and(Ast::is(['type' => 'node'], 'expr'))->toBeFalse()
		;
	});

	test('push', function () {
		$parent = Ast::tree('parent');
		$child = Ast::tree('child');
		$expr = Ast::expr('test');

		$child->push($expr);
		$parent->push($child);

		expect($parent->toArray())->toBe(Ast::tree('parent', [
			Ast::tree('child', [
				Ast::expr('test'),
			]),
		])->toArray());
	});

	test('visit', function () {
		$node = Ast::tree('node', [
			Ast::tree('child1', [
				Ast::tree('child2', [
					Ast::node('child3'),
				]),
			]),
			Ast::tree('child1', [
				Ast::node('child2'),
			]),
			Ast::node('child1'),
		]);

		$visited = [];

		Ast::visit($node, ['child1', 'child2', 'child3'], function ($node) use (&$visited) {
			$visited[] = $node;
		});

		expect($visited)->toHaveCount(6);
	});
});
