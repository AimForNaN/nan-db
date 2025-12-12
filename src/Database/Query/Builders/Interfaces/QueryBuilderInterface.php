<?php

namespace NaN\Database\Query\Builders\Interfaces;

interface QueryBuilderInterface {
	public function patch(): mixed;

	public function pull(): mixed;

	public function purge(): mixed;

	public function push(): mixed;
}
