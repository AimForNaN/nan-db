<?php

namespace NaN\Database;

final class Ast {
	const bool CONTINUE = true;
	const bool EXIT = false;
	const string SKIP = 'skip';

	/**
	 * @param string $expr
	 * @param string|null $operator
	 * @param mixed|null $value
	 * @param Quotes $quotes Whether to use quotations and what kind. Defaults to `None`.
	 *
	 * @return array
	 */
	public static function expr(
		string $expr,
		?string $operator = null,
		mixed $value = null,
		array $quotes = [Quotes::None, Quotes::None, Quotes::None],
	): array {
		$parts = [$expr];

		if (!empty($operator)) {
			$parts[] = $operator;
		}

		if ($value !== null) {
			$parts[] = $value;
		}

		return [
			'parts' => $parts,
			'quotes' => $quotes,
			'type' => 'expression',
		];
	}

	public static function group(array $children = []): array {
		return [
			'children' => $children,
			'type' => 'group',
		];
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
		array $node,
		array|callable|string|null $test = null,
		string|int|null $index = null,
		?array $parent = null,
		mixed $context = null,
	): bool {
		$check = self::_convert($test);
		$check = $check->bindTo($context);

		return isset($node['type']) && \is_string($node['type'])
			? $check($node, $index, $parent)
			: false
		;
	}

	public static function list(array $children = []): array {
		return [
			'children' => $children,
			'type' => 'list',
		];
	}

	public static function node(string $name, array $children = []): array {
		return [
			'children' => $children,
			'name' => $name,
			'type' => 'node',
		];
	}

	public static function push(array &$child, array &$parent): void {
		$parent['children'][] = &$child;
	}

	public static function pushChildren(array $from, array &$to): void {
		foreach ($from['children'] as $child) {
			self::push($child, $to);
		}
	}

	/**
	 * Inspired by unist-util-visit.
	 *
	 * @param array $node
	 * @param array|callable|string|null $test
	 * @param callable $visitor
	 *
	 * @return void
	 */
	public static function visit(
		array $node,
		array|callable|string|null $test,
		callable $visitor,
	): void {
		if (\is_callable($test) && !\is_callable($visitor)) {
			$test = null;
			$visitor = $test;
		}

		$parent_visitor = function ($node, array $parents) use ($visitor) {
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
				return function ($node) use ($test) {
					$tests = \array_map(fn($test) => self::_convert($test), $test);

					return \array_any($tests, fn($test) => $test($node));
				};
			}

			return function ($node) use ($test) {
				foreach ($test as $key => $value) {
					if ($value !== $node[$key]) {
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
			return fn($node) => $node['type'] === $test;
		}

		return fn() => true;
	}

	/**
	 * Inspired by unist-util-visit-parents.
	 *
	 * @param array $node
	 * @param array|callable|string|null $test
	 * @param bool|callable $visitor
	 *
	 * @return void
	 */
	protected static function _visitParents(
		array $node,
		array|callable|string|null $test,
		callable $visitor,
	): void {
		$check = null;

		if (\is_callable($test) && !\is_callable($visitor)) {
			$visitor = $test;
		} else {
			$check = $test;
		}

		/** @var callable(array, array|callable|string|null, string|int|null, mixed): bool $is */
		$is = self::_convert($check);

		$visit = function (
			array $node,
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

			if (\isset($node['children']) && \is_array($node['children']) && $result !== self::SKIP) {
				foreach ($node['children'] as $child) {
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
