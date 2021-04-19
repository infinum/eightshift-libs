<?php

declare(strict_types=1);

namespace Tests\Datasets\Autowiring\Services;

use EightshiftLibs\Services\ServiceInterface;
use Tests\Datasets\Autowiring\Dependencies\ClassWithDependency;

class ServiceWithDeepClassDep implements ServiceInterface
{

	public function __construct(ClassWithDependency $classWithDependency) {
		$this->classWithDependency = $classWithDependency;
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
