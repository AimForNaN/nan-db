<?php

namespace NaN\Database\Sql\Query\Statements;

use NaN\Database\Query\Renderers\Interfaces\RendererInterface;

class Raw implements Interfaces\SqlStatementInterface {
	use Traits\SqlStatementTrait;

	protected RendererInterface $_renderer;

	public function __construct(
		string $sql,
		protected array $_bindings = [],
	) {
		$this->_data = $sql;
		$this->_renderer = new class() implements RendererInterface {
			public function render(mixed $data): string {
				return $data;
			}
		};
	}

	public function getBindings(): array {
		return $this->_bindings;
	}
}
