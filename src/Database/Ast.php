<?php

namespace NaN\Database;

use NaN\Database\Ast\{Collection, Group, Node, Tree};

final class Ast {
	public static function __callStatic(string $name, array $args) {
		return self::tree($name, ...$args);
	}

	public static function group(array $children = []): Tree {
		return new Group('group', $children);
	}

	public static function identifier(string $identifier, $quotes = Quotes::Backtick): Node {
		return self::node('identifier', [
			'quotes' => $quotes,
			'value' => $identifier,
		]);
	}

	/**
	 * Inspired by unist-util-is.
	 *
	 * @param array $node
	 * @param array|callable|string|null $test
	 * @param string|int|null $index
	 * @param array|null $parent
	 * @param mixed|null $context
	 *
	 * @return bool
	 */
	public static function is(
		mixed $node,
		array|callable|string|null $test = null,
		string|int|null $index = null,
		?array $parent = null,
		mixed $context = null,
	): bool {
		if ($node instanceof Node) {
			$check = self::_convert($test);
			$check = $check->bindTo($context);

			return $check($node, $index, $parent);
		}

		return false;
	}

	public static function list(array $children = []): Tree {
		return new Collection('list', $children);
	}

	public static function match(string $identifier, string $operator, mixed $value, Quotes $quotes = Quotes::None): Tree {
		return self::expression([
			Ast::identifier($identifier),
			Ast::raw(' '),
			Ast::raw($operator),
			Ast::raw(' '),
			Ast::value($value, $quotes, true),
		]);
	}

	public static function node(string $type, array $data = []): Node {
		return new Node($type, $data);
	}

	public static function pushChildren(Tree $from, Tree $to): void {
		foreach ($from as $child) {
			$to->push($child);
		}
	}

	public static function raw(string $raw): Node {
		return self::node('raw', [
			'value' => $raw,
		]);
	}

	public static function space(): Node {
		return self::node('space');
	}

	public static function tree(string $type, array $children = [], array $data = []): Tree {
		return new Tree($type, $children, $data);
	}

	public static function unshift(Tree $child, Tree $parent): void {
		$parent->unshift($child);
	}

	public static function value(mixed $value, Quotes $quotes = Quotes::None, bool $prepare = false): Node {
		if (\is_array($value)) {
			return self::group([
				self::list(\array_map(fn($value) => self::value($value, $quotes, $prepare), $value)),
			]);
		}

		return self::node('value', [
			'prepare' => $prepare,
			'quotes' => $quotes,
			'value' => $value,
		]);
	}

	/**
	 * Inspired by unist-util-visit.
	 *
	 * @param Tree $node
	 * @param array|callable|string|null $test
	 * @param callable $visitor
	 *
	 * @return void
	 */
	public static function visit(
		Tree $node,
		array|callable|string|null $test,
		callable $visitor,
	): void {
		$is = self::_convert($test);

		$filtered = new \CallbackFilterIterator($node->getIterator(), function ($current) use ($is) {
			return $is($current);
		});

		foreach ($filtered as $child) {
			$visitor($child);
		}
	}

	/**
	 * Inspired by unist-util-is.
	 *
	 * @param array|callable|string|null $test
	 *
	 * @return \Closure
	 */
	protected static function _convert(array|callable|string|null $test = null): \Closure {
		if (\is_array($test)) {
			if (\array_is_list($test)) {
				return function (Node $node) use ($test) {
					$tests = \array_map(fn($test) => self::_convert($test), $test);

					return \array_any($tests, fn($test) => $test($node));
				};
			}

			return function (Node $node) use ($test) {
				foreach ($test as $key => $value) {
					$node_value = $node->{$key};

					if ($value !== $node_value) {
						return false;
					}
				}

				return true;
			};
		}

		if (\is_callable($test)) {
			return $test;
		}

		if (\is_string($test)) {
			return fn(Node $node) => $node->type === $test;
		}

		return fn() => true;
	}
}
