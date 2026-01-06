<?php

namespace NaN\Database\Query\Statements\Traits;

use NaN\Database\Interfaces\ConnectionInterface;
use NaN\Database\Query\Renderers\Interfaces\RendererInterface;

trait StatementTrait {
	protected mixed $_data;

	public function __construct(
		protected RendererInterface $_renderer,
	) {
	}

	public function __toString(): string {
		return $this->_renderer->render($this->_data);
	}

	public function exec(ConnectionInterface $connection): mixed {
		$query = $this->__toString();
		return $connection->exec($query);
	}
}
