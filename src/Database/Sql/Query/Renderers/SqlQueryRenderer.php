<?php

namespace NaN\Database\Sql\Query\Renderers;

use NaN\Database\Ast\{Node,Tree};
use NaN\Database\Query\Statements\Interfaces\ClauseInterface;
use NaN\Database\Query\Renderers\Interfaces\RendererInterface;
use NaN\Database\Quotes;

class SqlQueryRenderer implements RendererInterface {
	public function render(Node $ast): string {
		$ret = '';

		if ($ast instanceof Tree) {
			$gen = $this->_generate($ast);

			foreach ($gen as $node) {
				$ret .= $node;
			}
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
					yield $child->prepare ? '?' : $this->_handleQuotes($child->value, $child->quotes);
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

		switch ($quotes) {
			case Quotes::Auto:
				return match (gettype($value)) {
					'string' => $this->_handleQuotes($value, Quotes::Single),
					default => $value,
				};
				break;
			case Quotes::Backtick:
				return '`' . $value . '`';
			case Quotes::Double:
				return '"' . $value . '"';
			case Quotes::Single:
				return '\'' . $value . '\'';
		}

		return $value;
	}
}
