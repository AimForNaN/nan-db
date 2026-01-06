<?php

namespace NaN\Database\Drivers\Interfaces;

use NaN\Database\Query\Builders\Interfaces\QueryBuilderInterface;

interface DriverInterface {
	public function createConnection(array $driver_config = []): mixed;
	public function createQueryBuilder(): QueryBuilderInterface;
}
