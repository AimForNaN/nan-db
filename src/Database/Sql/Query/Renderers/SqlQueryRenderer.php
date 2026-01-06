<?php

namespace NaN\Database\Sql\Query\Renderers;

use NaN\Database\Ast;
use NaN\Database\Ast\{Node,Tree};
use NaN\Database\Query\Renderers\Interfaces\RendererInterface;
use NaN\Database\Quotes;

class SqlQueryRenderer implements RendererInterface {
	public function render(mixed $data): string {
		$ret = $this->_handle($data);

		return \implode(' ', $ret);
	}

	protected function _getParent(array $parents): ?array {
		$key = \array_key_last($parents);

		return $parents[$key] ?? null;
	}

	protected function _handle(Node $node, array $parents = []): array {
		switch ($node->type) {
			case 'set': {
				$node = $this->_transformSetClause($node, $parents);
				break;
			}
			case 'values': {
				$node = $this->_transformValuesClause($node, $parents);
				break;
			}
			case 'where': {
				$node = $this->_transformWhereClause($node, $parents);
				break;
			}
		}

		switch ($node->type) {
			case 'expression': {
				return $this->_handleExpression($node, $parents);
			}
			case 'group': {
				/** @var Tree $node */
				return ['(' . \implode(' ', $this->_handleChildren($node, [...$parents, $node])) . ')'];
			}
			case 'list': {
				/** @var Tree $node */
				return [\implode(', ', $this->_handleChildren($node, [...$parents, $node]))];
			}
		}

		$children = [];

		/** @var Tree $node */
		if (\count($node)) {
			$children = $this->_handleChildren($node, [...$parents, $node]);
		}

		return [
			\strtoupper($node->type),
			...$children,
		];
	}

	protected function _handleChildren(Tree $node, array $parents = []): array {
		$ret = [];

		foreach ($node as $child) {
			\array_push($ret, ...$this->_handle($child, $parents));
		}

		return $ret;
	}

	protected function _handleExpression(Node $node, array $parents = []): array {
		[$parts, $quotes] = $node->getData('parts', 'quotes');
		[$expr, $operator, $value] = $parts + [null, null, null];
		[$expr_quotes, $operator_quotes, $value_quotes] = $quotes;

		$expr = $this->_handleQuotes($expr, $expr_quotes);
		$operator = $this->_handleQuotes($operator, $operator_quotes);
		$value = $this->_handleQuotes($value, $value_quotes);

		if (\is_array($value)) {
			$value = '(' . \implode(', ', $value) . ')';
		}

		$separator = ' ';

		if ($operator === '.') {
			$separator = '';
		}

		return [\implode($separator, \array_filter([$expr, $operator, $value], fn ($v) => !\is_null($v)))];
	}

	protected function _handleQuotes(mixed $value, Quotes $quotes = Quotes::None): mixed {
		if (\is_array($value)) {
			return \array_map(fn ($v) => $this->_handleQuotes($v, $quotes), $value);
		}

		if ($value === '?') {
			return $value;
		}

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

	protected function _handleValue(mixed $value): mixed {
		if (\is_array($value)) {
			return \array_map(fn($v) => '?', $value);
		}

		return \is_null($value) ? $value : '?';
	}

	protected function _handleValueExpression(Node $node, array $parents = []): Node {
		[$parts, $quotes] = $node->getData('parts', 'quotes');
		[$expr, $operator, $value] = $parts;
		[$expr_quotes, $operator_quotes] = $quotes;
		$value = $this->_handleValue($value);
		return Ast::expr($expr, $operator, $value, [$expr_quotes, $operator_quotes, Quotes::None]);
	}

	protected function _transformSetClause(Node $node, array $parents): Node {
		$children = [];

		foreach ($node as $child) {
			if (Ast::is($child, 'expression')) {
				$children[] = $this->_handleValueExpression($child, [...$parents, $node]);
				continue;
			}

			$children[] = $child;
		}

		$children = [Ast::list($children)];

		return Ast::tree('set', $children);
	}

	protected function _transformWhereClause(Node $node, array $parents = []): Node {
		$ret = Ast::tree('where');

		foreach ($node as $child) {
			if (Ast::is($child, 'expression')) {
				$child = $this->_handleValueExpression($child, [...$parents, $node]);
			} else if (Ast::is($child, 'where')) {
				/** @var Tree $where */
				$where = $this->_transformWhereClause($child, [...$parents, $node]);
				$child = $where->withType('group');
			}

			$ret->push($child);
		}

		return $ret;
	}

	protected function _transformValuesClause(Tree $node, array $parents): Node {
		$children = [];

		foreach ($node as $child) {
			if (Ast::is($child, 'expression')) {
				$children[] = $this->_handleValueExpression($child, [...$parents, $node]);
				continue;
			}

			$children[] = $child;
		}

		$children = [Ast::group([
			Ast::list($children),
		])];

		return Ast::tree('values', $children);
	}
}
