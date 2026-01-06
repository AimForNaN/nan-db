<?php

namespace NaN\Database\Query\Renderers\Interfaces;

interface RendererInterface {
	public function render(mixed $data): string;
}
