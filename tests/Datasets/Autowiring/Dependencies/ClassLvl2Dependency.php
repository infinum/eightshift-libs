<?php

declare(strict_types=1);

namespace Tests\Datasets\Autowiring\Dependencies;

class ClassLvl2Dependency
{
  public function __construct(ClassLvl3Dependency $classLvl3Dependency) {
    $this->classLvl3Dependency = $classLvl3Dependency;
  }
}
