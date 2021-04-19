<?php

declare(strict_types=1);

namespace Tests\Datasets\Autowiring\Dependencies;

class ClassWithDependency
{
  public function __construct(ClassDepWithNoDependencies $classDepWithNoDependencies) {
    $this->classDepWithNoDependencies = $classDepWithNoDependencies;
  }
}
