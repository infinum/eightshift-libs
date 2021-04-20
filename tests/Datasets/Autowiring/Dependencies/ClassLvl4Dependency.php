<?php

declare(strict_types=1);

namespace Tests\Datasets\Autowiring\Dependencies;

class ClassLvl4Dependency
{
  public function __construct(ClassLvl5Dependency $classLvl5Dependency) {
    $this->classLvl5Dependency = $classLvl5Dependency;
  }
}
