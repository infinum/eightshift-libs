<?php

declare(strict_types=1);

namespace Tests\Datasets\Autowiring\Services;

use EightshiftLibs\Services\ServiceInterface;

class ServiceNoDependencies implements ServiceInterface
{
	/**
	 * Registers service.
   *
	 * @return void
	 */
	public function register(): void
	{
	}
}
