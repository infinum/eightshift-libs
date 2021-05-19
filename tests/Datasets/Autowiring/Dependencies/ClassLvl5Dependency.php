<?php

declare(strict_types=1);

namespace Tests\Datasets\Autowiring\Dependencies;

class ClassLvl5Dependency
{
  public function __construct(ClassLvl6Dependency $classLvl6Dependency) {
    $this->classLvl6Dependency = $classLvl6Dependency;
  }
}
