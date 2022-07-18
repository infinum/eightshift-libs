<?php

namespace Tests\Unit\Autowiring;

use EightshiftBoilerplate\Main\MainExample;
use EightshiftLibs\Exception\{InvalidAutowireDependency, NonPsr4CompliantClass};
use EightshiftLibs\Helpers\Components;
use Tests\Datasets\Autowiring\Dependencies\{ClassDepWithNoDependencies,
	ClassImplementingInterfaceDependency,
	ClassLvl1Dependency,
	ClassLvl2Dependency,
	ClassLvl3Dependency,
	ClassLvl4Dependency,
	ClassLvl5Dependency,
	ClassLvl6Dependency,
	ClassLvl7Dependency,
	ClassWithDependency,
	InterfaceDependency
};
use Tests\Datasets\Autowiring\Dependencies\SubNamespace1\SomeClass;
use Tests\Datasets\Autowiring\Services\{
	ServiceWithClassDep,
	ServiceWithDeepClassDep,
	ServiceWithDeepDependencyTree,
	ServiceWithInterfaceDep,
	ServiceWithInterfaceDepMoreThanOneClassFound,
	ServiceWithInterfaceDepWrongName,
	ServiceWithMultipleDeps,
	ServiceWithPrimitiveDep,
	ServiceWithPrimitiveDepHasDefault
};

beforeEach(function() {

	$this->main = new MainExample([
		'Tests\\Datasets\\Autowiring\\' => [
			Components::getProjectPaths('projectRoot', 'tests/Datasets/Autowiring')
		],
	], 'Tests\Datasets\Autowiring');

	$this->manuallyDefinedDependencies = [
		ServiceWithInterfaceDepWrongName::class => [
			ClassImplementingInterfaceDependency::class,
		],
		ServiceWithInterfaceDepMoreThanOneClassFound::class => [
			SomeClass::class,
		],
		ServiceWithPrimitiveDep::class => [
			'some string',
		],
		ServiceWithPrimitiveDepHasDefault::class => [
			'some string',
		]
	];

	$this->manualDepsNoPrimitive = [
		ServiceWithInterfaceDepWrongName::class => [
			ClassImplementingInterfaceDependency::class,
		],
		ServiceWithInterfaceDepMoreThanOneClassFound::class => [
			SomeClass::class,
		],
	];

	$this->manualDepsNoPrimitiveHasDefaults = [
		ServiceWithInterfaceDepWrongName::class => [
			ClassImplementingInterfaceDependency::class,
		],
		ServiceWithInterfaceDepMoreThanOneClassFound::class => [
			SomeClass::class,
		],
		ServiceWithPrimitiveDep::class => [
			'some string',
		],
	];
});

test('Building service classes works', function () {
	$dependencyTree = $this->main->buildServiceClasses($this->manuallyDefinedDependencies, true);

	expect($dependencyTree)
		->toBeArray()
		->not->toBeEmpty()
		->toHaveKey(ServiceWithInterfaceDepWrongName::class)
		->toHaveKey(ServiceWithInterfaceDepMoreThanOneClassFound::class)
		->toHaveKey(ServiceWithClassDep::class) // Is service with single class dependency auto-wired?
		->toHaveKey(ServiceWithDeepClassDep::class) // Service with 2 levels deep dependency tree.
		->toHaveKey(ClassWithDependency::class) // Is the lvl 1 class dependency auto-wired?
		->toHaveKey(ServiceWithInterfaceDep::class) // Is service with single interface dependency auto-wired?
		->toHaveKey(ServiceWithMultipleDeps::class) // Is service with 2 dependencies auto-wired?
		->toHaveKey(ServiceWithPrimitiveDep::class)
		->toHaveKey(ServiceWithPrimitiveDepHasDefault::class)
		->toHaveKey(ServiceWithDeepDependencyTree::class) // Deep dependencies are correctly autowired.
		->toHaveKey(ClassLvl1Dependency::class)
		->toHaveKey(ClassLvl2Dependency::class)
		->toHaveKey(ClassLvl3Dependency::class)
		->toHaveKey(ClassLvl4Dependency::class)
		->toHaveKey(ClassLvl5Dependency::class)
		->toHaveKey(ClassLvl6Dependency::class)
		->toContain(ClassLvl7Dependency::class)
		->not->toHaveKey(ServiceNoDe::class)
		// Autowiring should not touch abstract classes, interfaces and traits
		->not->toHaveKey(MockAbstractClass::class)
		->not->toHaveKey(InterfaceDependency::class)
		->not->toHaveKey(MockTrait::class)
		->and($dependencyTree[ServiceWithClassDep::class][0]) // Service with 1 level deep dependency tree.
		->toBe(ClassDepWithNoDependencies::class)
		->and($dependencyTree[ServiceWithDeepClassDep::class][0]) // Service with 2 level deep dependency tree.
		->toBe(ClassWithDependency::class)
		->and($dependencyTree[ClassWithDependency::class][0]) // Is the lvl 2 class dependency auto-wired?
		->toBe(ClassDepWithNoDependencies::class)
		->and($dependencyTree[ServiceWithInterfaceDep::class][0]) // Is service class dependency in the array of dependencies?
		->toBe(ClassImplementingInterfaceDependency::class)
		->and($dependencyTree[ServiceWithMultipleDeps::class][0]) // Is interface-based class dependency in the array of dependencies?
		->toBe(ClassImplementingInterfaceDependency::class)
		->and($dependencyTree[ServiceWithInterfaceDepWrongName::class][0])
		->toBe(ClassImplementingInterfaceDependency::class)
		->and($dependencyTree[ServiceWithInterfaceDepMoreThanOneClassFound::class][0])
		->toBe(SomeClass::class)
		->and($dependencyTree[ServiceWithPrimitiveDep::class][0])
		->toBe('some string')
		->and($dependencyTree[ServiceWithPrimitiveDepHasDefault::class][0])
		->toBe('some string')
		->and($dependencyTree[ServiceWithMultipleDeps::class][1]) // Is class dependency in the array of dependencies?
		->toBe(ClassDepWithNoDependencies::class)
		->not->toContain(ServiceWithPrimitiveDep::class); // Service classes with primitive dependencies are NOT auto-wired
});

test('Service classes with interface dependencies that cant be matched to exactly 1 class should throw exception.', function () {
	$this->main->buildServiceClasses([], true);
})->throws(InvalidAutowireDependency::class);

test('Services with Invalid namespace (non PSR-4 compliant) will not be auto-wired / included', function () {
	$this->main->buildServiceClasses($this->manuallyDefinedDependencies, false);
})->throws(NonPsr4CompliantClass::class);

test('Autowiring throws exception on primitive deps which are not manually configured', function () {
	$this->main->buildServiceClasses($this->manualDepsNoPrimitive, true);
})->throws(InvalidAutowireDependency::class);
