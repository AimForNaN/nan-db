<?php

namespace NaN\Database\Query\Renderers\Interfaces;

use NaN\Database\Ast\Node;

interface RendererInterface {
	public function render(Node $ast): string;
}
