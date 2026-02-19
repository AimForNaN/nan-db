<?php

namespace NaN\Database\Sql\Query\Renderers;

use NaN\Database\Ast\Node;
use NaN\Database\Ast\Tree;
use NaN\Database\Query\Statements\Interfaces\ClauseInterface;
use NaN\Database\Query\Renderers\Interfaces\RendererInterface;
use NaN\Database\Quotes;

class SqlQueryRenderer implements RendererInterface {
	public function render(ClauseInterface $statement): string {
		$data = $statement->toAst();
		$ret = '';

		if ($data instanceof Tree) {
			$gen = $this->_generate($data);

			foreach ($gen as $node) {
				$ret .= $node;
			}
		} else if ($data->type === 'raw') {
			$ret = $data->value;
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
					_ => $value,
				};
				break;
			case Quotes::Backtick:
				return '`' . $value . '`';
			case Quotes::Double:
				return '"' . $value . '"';
			case Quotes::Single:
				return '\'' . $value . '\'';
			default:
				return $value;
		}

		return null;
	}
}
