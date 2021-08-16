<?php

declare(strict_types=1);

namespace Tests\Datasets\Autowiring\Dependencies;

class ClassLvl6Dependency
{
  public function __construct(ClassLvl7Dependency $classLvl7Dependency) {
    $this->classLvl7Dependency = $classLvl7Dependency;
  }
}
