<?php

namespace NaN\Database\Drivers\Interfaces;

use NaN\Database\Query\Builders\Interfaces\QueryBuilderInterface;

/**
 * It's best to instantiate without a database name and let the system
 *  decide how to handle selecting databases according to each model.
 */
interface DriverInterface {
	public function createConnection(array $options = []): QueryBuilderInterface;
}
