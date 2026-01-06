<?php

namespace NaN\Database\Ast;

class Node {
	public function __construct(
		public readonly string $type = 'node',
		protected array $_data = [],
	) {
	}

	public function __get(string $name): mixed {
		return $this->_data[$name] ?? null;
	}

	public function __isset(string $name): bool {
		return isset($this->_data[$name]);
	}

	public function __set(string $name, mixed $value): void {
		$this->_data[$name] = $value;
	}

	public function getData(string ...$args): mixed {
		$ret = [];

		foreach ($args as $arg) {
			$ret[] = $this->_data[$arg] ?? null;
		}

		return \count($ret) === 1 ? $ret[0] : $ret;
	}

	public function toArray(): array {
		return [
			'data' => \array_map(fn($item) => $item instanceof Node ? $item->toArray() : $item, $this->_data),
			'type' => $this->type,
		];
	}

	public function withType(string $type): Node {
		return new Node($type, $this->_data);
	}
}
