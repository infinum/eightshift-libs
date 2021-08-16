<?php

declare(strict_types=1);

namespace Tests\Datasets\Autowiring\Services;

use EightshiftLibs\Services\ServiceInterface;
use Tests\Datasets\Autowiring\Dependencies\InterfaceDependency;

class ServiceWithInterfaceDepWrongName implements ServiceInterface
{
	public function __construct(InterfaceDependency $incorrectVariableName) {
		$this->incorrectVariableName = $incorrectVariableName;
	}

	/**
	 * Registers service.
   *
	 * @return void
	 */
	public function register(): void
	{
	}
}
