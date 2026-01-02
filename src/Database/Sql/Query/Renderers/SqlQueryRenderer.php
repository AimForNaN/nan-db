<?php

namespace NaN\Database\Sql\Query\Renderers;

use NaN\Database\Ast;
use NaN\Database\Query\Renderers\Interfaces\RendererInterface;
use NaN\Database\Quotes;

class SqlQueryRenderer implements RendererInterface {
	protected function _getParent(array $parents): ?array {
		$key = \array_key_last($parents);

		return $parents[$key] ?? null;
	}

	protected function _handle(array $node, array $parents = []): array {
		['type' => $type] = $node;
		$parent = $this->_getParent($parents);

		switch ($type) {
			case 'expression': {
				[
					'parts' => $parts,
					'quotes' => $quotes,
				] = $node;
				[$expr, $operator, $value] = $parts + [null, null, null];
				[$expr_quotes, $operator_quotes, $value_quotes] = $quotes + [Quotes::None, Quotes::None, Quotes::None];

				$expr = $this->_handleQuotes($expr, $expr_quotes);
				$operator = $this->_handleQuotes($operator, $operator_quotes);

				if (!empty($parent) && Ast::is($parent, [['name' => 'where'], ['name' => 'set']])) {
					if (\is_array($value)) {
						$value = '(' . \implode(',', \array_map(fn($v) => '?', $value)) . ')';
					} else if ($value !== null) {
						$value = '?';
					}
				} else {
					$value = $this->_handleQuotes($value, $value_quotes);
				}

				return \array_filter([$expr, $operator, $value], fn ($v) => $v !== null);
			}
			case 'group': {
				['children' => $children] = $node;

				return ['(' . \implode(' ', $this->_handleChildren($children, [...$parents, $node])) . ')'];
			}
			case 'list': {
				['children' => $children] = $node;

				return [\implode(', ', $this->_handle($children, [...$parents, $node]))];
			}
			case 'node': {
				[
					'children' => $children,
					'name' => $name,
				] = $node;

				if (!empty($children)) {
					$children = $this->_handleChildren($children, [...$parents, $node]);
				}

				if (
					!empty($children)
					&& $name == 'where'
					&& Ast::is($parent ?? [], ['name' => 'where', 'type' => 'node'])
				) {
					return ['(' . \implode(' ', $children) . ')'];
				} else {
					return [
						\strtoupper($name),
						...$children,
					];
				}
			}
		}

		return [];
	}

	protected function _handleChildren(array $children, array $parents = []): array {
		return \array_reduce($children, function (array $ret, array $child) use ($parents) {
			\array_push($ret, ...$this->_handle($child, $parents));

			return $ret;
		}, []);
	}

	protected function _handleQuotes(mixed $value, Quotes $quotes = Quotes::None): ?string {
		if (isset($value)) {
			switch ($quotes) {
				case Quotes::Backtick:
					return '`' . $value . '`';
				case Quotes::Double:
					return '"' . $value . '"';
				case Quotes::Single:
					return '\'' . $value . '\'';
				case Quotes::None:
					return $value;
			}
		}

		return null;
	}

	public function render(mixed $data): string {
		$ret = $this->_handle($data);

		return \implode(' ', $ret);
	}
}
