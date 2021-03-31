<?php

declare(strict_types=1);

namespace Tests\Datasets\Autowiring\Services;

use EightshiftLibs\Services\ServiceInterface;
use Tests\Datasets\Autowiring\Dependencies\ClassDepWithNoDependencies;

class ServiceWithClassDep implements ServiceInterface
{

	public function __construct(ClassDepWithNoDependencies $classDepWithNoDependencies) {
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
