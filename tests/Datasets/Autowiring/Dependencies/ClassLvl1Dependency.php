<?php

declare(strict_types=1);

namespace Tests\Datasets\Autowiring\Dependencies;

class ClassLvl1Dependency
{
	public function __construct(ClassLvl2Dependency $classLvl2Dependency) {
		$this->classLvl2Dependency = $classLvl2Dependency;
	}
}
