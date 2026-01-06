<?php

namespace NaN\Database\Ast;

class Tree extends Node implements \Countable, \IteratorAggregate {
	public function __construct(
		string $type = 'tree',
		protected array $_children = [],
		array $data = [],
	) {
		parent::__construct($type, $data);
	}

	public function count(): int {
		return count($this->_children);
	}

	public function getIterator(): \Traversable {
		return new \ArrayIterator($this->_children);
	}

	public function push(Node $node): Node {
		$this->_children[] = $node;

		return $this;
	}

	public function toArray(): array {
		return [
			'data' => \array_map(fn($item) => $item instanceof Node ? $item->toArray() : $item, $this->_data),
			'children' => \array_map(fn(Node $node) => $node->toArray(), $this->_children),
			'type' => $this->type,
		];
	}

	public function unshift(Node $node): Node {
		\array_unshift($this->_children, $node);

		return $this;
	}

	public function withType(string $type): Tree {
		return new Tree($type, $this->_children, $this->_data);
	}
}
