<?php

namespace NaN\Database\Sql\Query\Renderers;

use NaN\Database\Ast\{Node,Tree};
use NaN\Database\Query\Renderers\Interfaces\RendererInterface;
use NaN\Database\Quotes;
use NaN\Database\Sql\Query\Raw;

class SqlQueryRenderer implements RendererInterface {
	public function render(Node $ast): string {
		$ret = '';

		if ($ast instanceof Tree) {
			$gen = $this->_generate($ast);
			$ret = \iter\join('', $gen);
		} else if ($ast->type === 'raw') {
			$ret = $ast->value;
		}

		return \trim($ret);
	}

	protected function _generate(Node $node): \Generator {
		foreach ($node as $child) {
			switch ($child->type) {
				case 'identifier':
					yield $this->_handleQuotes($child->value, $child->quotes);
					break;
				case 'space':
					yield ' ';
					break;
				case 'value':
					yield $this->_handleValue($child);
					break;
				default:
					yield $child->value;
			}
		}
	}

	protected function _handleQuotes(mixed $value, Quotes $quotes): mixed {
		if ($value === '?') {
			return $value;
		}

		$search = ['"', '\'', '\\'];
		$replace = ['&quot;', '&apos;', '&bsol;'];

		switch ($quotes) {
			case Quotes::Auto:
				return match (gettype($value)) {
					'NULL' => 'NULL',
					'object' => (string)$value,
					'string' => $this->_handleQuotes($value, Quotes::Single),
					default => $value,
				};
			case Quotes::Backtick:
				return '`' . $value . '`';
			case Quotes::Double:
				return '"' . \str_replace($search, $replace, $value) . '"';
			case Quotes::Single:
				return '\'' . \str_replace($search, $replace, $value) . '\'';
		}

		return $value;
	}

	protected function _handleValue(Node $node): mixed {
		$value = $node->value;

		if (\is_null($value)) {
			return 'NULL';
		}

		if ($value instanceof Raw) {
			return (string)$value;
		}

		return $node->prepare ? '?' : $this->_handleQuotes($value, $node->quotes);
	}
}
