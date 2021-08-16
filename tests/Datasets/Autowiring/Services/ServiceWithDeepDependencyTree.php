<?php

declare(strict_types=1);

namespace Tests\Datasets\Autowiring\Services;

use EightshiftLibs\Services\ServiceInterface;
use Tests\Datasets\Autowiring\Dependencies\ClassLvl1Dependency;

class ServiceWithDeepDependencyTree implements ServiceInterface
{

	public function __construct(ClassLvl1Dependency $classLvl1Dependency) {
		$this->classLvl1Dependency = $classLvl1Dependency;
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
