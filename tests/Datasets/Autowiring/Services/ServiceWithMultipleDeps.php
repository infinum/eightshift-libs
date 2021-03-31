<?php

declare(strict_types=1);

namespace Tests\Datasets\Autowiring\Services;

use EightshiftLibs\Services\ServiceInterface;
use Tests\Datasets\Autowiring\Dependencies\ClassDepWithNoDependencies;
use Tests\Datasets\Autowiring\Dependencies\InterfaceDependency;

class ServiceWithMultipleDeps implements ServiceInterface
{
	public function __construct(InterfaceDependency $classImplementingInterfaceDependency, ClassDepWithNoDependencies $classDepWithNoDependencies) {
		$this->classImplementingInterfaceDependency = $classImplementingInterfaceDependency;
		$this->classDepWithNoDependencies = $classDepWithNoDependencies;
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
