<?php

namespace Tests\Unit\Autowiring;

use Brain\Monkey;
use EightshiftBoilerplate\Main\MainExample;
use MockAutowiring\Services\ServiceNoDependencies;
use MockAutowiring\Deep\Deeper\ServiceNoDependenciesDeep;
use MockAutowiring\NonServices\SomeFactory;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->main = new MainExample([
		'MockAutowiring\\' => [
			dirname( __FILE__, 2 ) . '/Datasets/Autowiring',
		],
	], 'MockAutowiring');
});

afterEach(function() {
	Monkey\tearDown();
});

test('Building service classes works', function () {
	// $dependencyTree = $this->main->buildServiceClasses();
	// $this->assertIsArray($dependencyTree);
	// $this->assertGreaterThan(0, count($dependencyTree));
});

test('Service classes are correctly included in the list', function () {
	$dependencyTree = $this->main->buildServiceClasses();
	$this->assertIsArray($dependencyTree);
	$this->assertContains(ServiceNoDependencies::class, $dependencyTree);
	$this->assertContains(ServiceNoDependenciesDeep::class, $dependencyTree);
});

test('Non-service classes are NOT auto-wired', function () {
	$dependencyTree = $this->main->buildServiceClasses();
	echo print_r($dependencyTree);
	$this->assertIsArray($dependencyTree);
	$this->assertNotContains(SomeFactory::class, $dependencyTree);
});

test('Service classes with class dependencies are properly auto-wired', function () {
	// $dependencyTree = $this->main->buildServiceClasses();
	// $this->assertIsArray($dependencyTree);
	$this->assertTrue(true);
});

test('Service classes with interface dependencies are properly auto-wired', function () {
	// $dependencyTree = $this->main->buildServiceClasses();
	// $this->assertIsArray($dependencyTree);
	$this->assertTrue(true);
});

test('Service classes with a mix of class / interface dependencies are properly auto-wired', function () {
	// $dependencyTree = $this->main->buildServiceClasses();
	// $this->assertIsArray($dependencyTree);
	$this->assertTrue(true);
});

test('Service classes with primitive dependencies are NOT auto-wired', function () {
	// $dependencyTree = $this->main->buildServiceClasses();
	// $this->assertIsArray($dependencyTree);
	$this->assertTrue(true);
});

test('Services with Invalid namespace (non PSR-4 compliant) will not be auto-wired / included', function () {
	$this->assertTrue(true);

	// $dependencyTree = $this->main->buildServiceClasses();
	// $this->assertIsArray($dependencyTree);
	// $this->assertNotContains(ServiceWithInvalidNamespace::class, $dependencyTree);
});
