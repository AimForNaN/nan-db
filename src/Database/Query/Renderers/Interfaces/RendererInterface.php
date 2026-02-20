<?php

namespace NaN\Database\Query\Renderers\Interfaces;

use NaN\Database\Query\Statements\Interfaces\ClauseInterface;

interface RendererInterface {
	public function render(ClauseInterface $clause): string;
}
