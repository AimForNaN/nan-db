<?php

namespace NaN\Database\Query\Statements\Interfaces;

interface WhereClauseInterface {
	public function where(\Closure $fn): static;
}
