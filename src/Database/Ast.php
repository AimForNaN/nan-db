<?php

namespace NaN\Database;

use NaN\Database\Ast\{Node,Tree};

final class Ast {
	const bool CONTINUE = true;
	const bool EXIT = false;
	const string SKIP = 'skip';

	/**
	 * @todo Maybe implement a more complex structure!
	 *
	 * @param string|null $expr
	 * @param string|null $operator
	 * @param mixed|null $value
	 * @param array|Quotes $quotes Whether to use quotations and what kind. Defaults to `Auto`.
	 *
	 * @return Node
	 */
	public static function expr(
		?string $expr = null,
		?string $operator = null,
		mixed $value = null,
		array|Quotes $quotes = Quotes::Auto,
	): Node {
		if ($quotes instanceof Quotes) {
			if ($quotes === Quotes::Auto) {
				$quotes = [Quotes::Backtick, Quotes::None];

				$value_quotes = match (\gettype($value)) {
					'string' => Quotes::Double,
					default => Quotes::None,
				};

				$quotes[] = $value_quotes;
			} else {
				$quotes = \array_fill(0, 3, $quotes);
			}
		}

		return new Node('expression', [
			'parts' => [$expr, $operator, $value],
			'quotes' => $quotes + [Quotes::None, Quotes::None, Quotes::None],
		]);
	}

	public static function group(array $children = []): Tree {
		return new Tree('group', $children);
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
		return new Tree('list', $children);
	}

	public static function node(string $type, array $data = []): Node {
		return new Node($type, $data);
	}

	public static function pushChildren(Tree $from, Tree $to): void {
		foreach ($from as $child) {
			$to->push($child);
		}
	}

	public static function tree(string $type, array $children = [], array $data = []): Tree {
		return new Tree($type, $children, $data);
	}

	public static function unshift(Tree $child, Tree $parent): void {
		$parent->unshift($child);
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
		Node $node,
		array|callable|string|null $test,
		callable $visitor,
	): void {
		if (\is_callable($test) && !\is_callable($visitor)) {
			$test = null;
			$visitor = $test;
		}

		$parent_visitor = function (Node $node, array $parents) use ($visitor) {
			$parent = \end($parents) ?: null;
			[$key] = \is_array($parent) ? \array_keys($parent, $node) : [null];
			return $visitor($node, $key, $parent);
		};

		self::_visitParents($node, $test, $parent_visitor);
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

	/**
	 * Inspired by unist-util-visit-parents.
	 *
	 * @param Tree $node
	 * @param array|callable|string|null $test
	 * @param callable $visitor
	 *
	 * @return void
	 */
	protected static function _visitParents(
		Node $node,
		array|callable|string|null $test,
		callable $visitor,
	): void {
		$check = null;

		if (\is_callable($test) && !\is_callable($visitor)) {
			$visitor = $test;
		} else {
			$check = $test;
		}

		/** @var callable(Node, array|callable|string|null, string|int|null, mixed): bool $is */
		$is = self::_convert($check);

		$visit = function (
			Node $node,
			string|int|null $index = null,
			array $parents = [],
		) use ($is, $test, $visitor, &$visit) {
			$result = self::CONTINUE;
			$parent = \end($parents) ?: null;

			if (\is_null($test) || $is($node, $index, $parent)) {
				$result = $visitor($node, $parents);

				if ($result === self::EXIT) {
					return $result;
				}
			}

			if ($node instanceof Tree && $result !== self::SKIP) {
				foreach ($node as $child) {
					$result = $visit($child, $index, [$node]);

					if ($result === self::EXIT) {
						return $result;
					}
				}
			}

			return $result;
		};

		$visit($node);
	}
}
