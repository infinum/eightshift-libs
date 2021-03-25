<?php

declare(strict_types=1);

namespace MockAutowiring\Services;

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
