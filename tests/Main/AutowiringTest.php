<?php

namespace Tests\Unit\Autowiring;

use Brain\Monkey;
use EightshiftBoilerplate\Main\MainExample;
use EightshiftLibs\Exception\InvalidAutowireDependency;
use EightshiftLibs\Exception\NonPsr4CompliantClass;
use Tests\Datasets\Autowiring\Services\ServiceNoDependencies;
use Tests\Datasets\Autowiring\Deep\Deeper\ServiceNoDependenciesDeep;
use Tests\Datasets\Autowiring\Dependencies\ClassDepWithNoDependencies;
use Tests\Datasets\Autowiring\Dependencies\ClassImplementingInterfaceDependency;
use Tests\Datasets\Autowiring\Dependencies\ClassWithDependency;
use Tests\Datasets\Autowiring\Dependencies\InterfaceDependency;
use Tests\Datasets\Autowiring\Dependencies\SubNamespace1\SomeClass;
use Tests\Datasets\Autowiring\NonServices\SomeFactory;
use Tests\Datasets\Autowiring\Services\ServiceWithClassDep;
use Tests\Datasets\Autowiring\Services\ServiceWithDeepClassDep;
use Tests\Datasets\Autowiring\Services\ServiceWithInterfaceDep;
use Tests\Datasets\Autowiring\Services\ServiceWithInterfaceDepMoreThanOneClassFound;
use Tests\Datasets\Autowiring\Services\ServiceWithInterfaceDepWrongName;
use Tests\Datasets\Autowiring\Services\ServiceWithMultipleDeps;
use Tests\Datasets\Autowiring\Services\ServiceWithPrimitiveDep;
use Tests\Datasets\Autowiring\Services\ServiceWithPrimitiveDepHasDefault;

beforeEach(function() {

	$this->main = new MainExample([
		'Tests\\Datasets\\Autowiring\\' => [
			dirname( __FILE__, 2 ) . '/Datasets/Autowiring',
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
	$this->assertIsArray($dependencyTree);
	$this->assertGreaterThan(0, count($dependencyTree));
});

test('Service classes are correctly included in the list', function () {
	$dependencyTree = $this->main->buildServiceClasses($this->manuallyDefinedDependencies, true);
	$this->assertIsArray($dependencyTree);
	$this->assertContains(ServiceNoDependencies::class, $dependencyTree);
	$this->assertContains(ServiceNoDependenciesDeep::class, $dependencyTree);
});

test('Non-service classes are NOT auto-wired', function () {
	$dependencyTree = $this->main->buildServiceClasses($this->manuallyDefinedDependencies, true);
	$this->assertIsArray($dependencyTree);
	$this->assertNotContains(SomeFactory::class, $dependencyTree);
});

test('Service classes with class dependencies are properly auto-wired', function () {
	$dependencyTree = $this->main->buildServiceClasses($this->manuallyDefinedDependencies, true);
	$this->assertIsArray($dependencyTree);

	// Service with 1 level deep dependency tree.
	$this->assertArrayHasKey(ServiceWithClassDep::class, $dependencyTree, 'Is service with single class dependency auto-wired?');
	$this->assertContains(ClassDepWithNoDependencies::class, $dependencyTree[ServiceWithClassDep::class], 'Is service class dependency in the array of dependencies?');

	// Service with 2 levels deep dependency tree.
	$this->assertArrayHasKey(ServiceWithDeepClassDep::class, $dependencyTree, 'Is service with single class dependency (which has its own dependency) auto-wired?');
	$this->assertContains(ClassWithDependency::class, $dependencyTree[ServiceWithDeepClassDep::class], 'Is service class dependency in the array of dependencies?');
	$this->assertArrayHasKey(ClassWithDependency::class, $dependencyTree, 'Is the lvl 1 class dependency auto-wired?');
	$this->assertContains(ClassDepWithNoDependencies::class, $dependencyTree[ClassWithDependency::class], 'Is the lvl 2 class dependency auto-wired?');
});

test('Service classes with interface dependencies are properly auto-wired', function () {
	$dependencyTree = $this->main->buildServiceClasses($this->manuallyDefinedDependencies, true);
	$this->assertIsArray($dependencyTree);
	$this->assertArrayHasKey(ServiceWithInterfaceDep::class, $dependencyTree, 'Is service with single interface dependency auto-wired?');
	$this->assertContains(ClassImplementingInterfaceDependency::class, $dependencyTree[ServiceWithInterfaceDep::class], 'Is service class dependency in the array of dependencies?');
});

test('Service classes with multiple dependencies are properly auto-wired', function () {
	$dependencyTree = $this->main->buildServiceClasses($this->manuallyDefinedDependencies, true);
	$this->assertIsArray($dependencyTree);
	$this->assertArrayHasKey(ServiceWithMultipleDeps::class, $dependencyTree, 'Is service with 2 dependencies auto-wired?');
	$this->assertContains(ClassImplementingInterfaceDependency::class, $dependencyTree[ServiceWithMultipleDeps::class], 'Is interface-based class dependency in the array of dependencies?');
	$this->assertContains(ClassDepWithNoDependencies::class, $dependencyTree[ServiceWithMultipleDeps::class], 'Is class dependency in the array of dependencies?');
});

test('Service classes with primitive dependencies are NOT auto-wired', function () {
	$dependencyTree = $this->main->buildServiceClasses($this->manuallyDefinedDependencies, true);
	$this->assertIsArray($dependencyTree);
	$this->assertNotContains(ServiceWithPrimitiveDep::class, $dependencyTree);
});

test('Service classes with interface dependencies that cant be matched to exactly 1 class should throw exception.', function () {
	$this->main->buildServiceClasses([], true);
})->throws(InvalidAutowireDependency::class);

test('Services with Invalid namespace (non PSR-4 compliant) will not be auto-wired / included', function () {
	$this->main->buildServiceClasses($this->manuallyDefinedDependencies, false);
})->throws(NonPsr4CompliantClass::class);

test('Autowiring should not touch abstract classes, interfaces and traits', function () {
	$dependencyTree = $this->main->buildServiceClasses($this->manuallyDefinedDependencies, true);
	$this->assertIsArray($dependencyTree);
	$this->assertNotContains(MockAbstractClass::class, $dependencyTree);
	$this->assertNotContains(InterfaceDependency::class, $dependencyTree);
	$this->assertNotContains(MockTrait::class, $dependencyTree);
});

test('Autowiring does not throw exceptions on blocks', function () {
	$this->main->buildServiceClasses($this->manuallyDefinedDependencies, true);
	$this->assertTrue(true);
});

test('Autowiring throws exception on primitive deps which are not manually configured', function () {
	$this->main->buildServiceClasses($this->manualDepsNoPrimitive, true);
})->throws(InvalidAutowireDependency::class);

test('buildServiceClasses includes all manually defined dependency trees', function () {
	$dependencyTree = $this->main->buildServiceClasses($this->manuallyDefinedDependencies, true);
	$this->assertArrayHasKey(ServiceWithInterfaceDepWrongName::class, $dependencyTree);
	$this->assertArrayHasKey(ServiceWithInterfaceDepMoreThanOneClassFound::class, $dependencyTree);
	$this->assertArrayHasKey(ServiceWithPrimitiveDep::class, $dependencyTree);
	$this->assertArrayHasKey(ServiceWithPrimitiveDepHasDefault::class, $dependencyTree);
	$this->assertIsArray($dependencyTree[ServiceWithInterfaceDepWrongName::class]);
	$this->assertIsArray($dependencyTree[ServiceWithInterfaceDepMoreThanOneClassFound::class]);
	$this->assertIsArray($dependencyTree[ServiceWithPrimitiveDep::class]);
	$this->assertIsArray($dependencyTree[ServiceWithPrimitiveDepHasDefault::class]);
	$this->assertContains(ClassImplementingInterfaceDependency::class, $dependencyTree[ServiceWithInterfaceDepWrongName::class] );
	$this->assertContains(SomeClass::class, $dependencyTree[ServiceWithInterfaceDepMoreThanOneClassFound::class] );
	$this->assertContains('some string', $dependencyTree[ServiceWithPrimitiveDep::class] );
	$this->assertContains('some string', $dependencyTree[ServiceWithPrimitiveDepHasDefault::class] );
});
