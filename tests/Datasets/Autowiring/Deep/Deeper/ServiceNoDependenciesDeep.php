<?php

declare(strict_types=1);

namespace MockAutowiring\Deep\Deeper;

use EightshiftLibs\Services\ServiceInterface;

class ServiceNoDependenciesDeep implements ServiceInterface
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
