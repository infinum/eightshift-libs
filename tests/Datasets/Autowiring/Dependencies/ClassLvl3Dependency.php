<?php

declare(strict_types=1);

namespace Tests\Datasets\Autowiring\Dependencies;

class ClassLvl3Dependency
{
	public function __construct(ClassLvl4Dependency $classLvl4Dependency) {
		$this->classLvl4Dependency = $classLvl4Dependency;
	}
}
