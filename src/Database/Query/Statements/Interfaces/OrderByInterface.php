<?php

namespace NaN\Database\Query\Statements\Interfaces;

interface OrderByInterface {
	public function orderBy(array $order): static;
}
