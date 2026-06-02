<?php

namespace NaN\Database\Drivers\Interfaces;

use NaN\Database\Interfaces\ConnectionInterface;

interface DriverInterface {
	public function createConnection(array $driver_config = []): ConnectionInterface;
}
