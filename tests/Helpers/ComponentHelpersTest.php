<?php

namespace Tests\Unit\Helpers;

use EightshiftLibs\Exception\ComponentException;
use EightshiftLibs\Helpers\Components;

/**
 * Components::ensureString tests
 */
test('Asserts ensure string returns a correct result', function ($args) {
	$this->assertIsString(Components::ensureString($args));
})->with('correctArguments');


test('Throws type exception if wrong argument type is passed to ensureString',
	function ($argument) {
		Components::ensureString($argument);
	})
	->throws(\TypeError::class)
	->with('errorStringArguments');


test('Throws argument count exception if no argument is passed', function () {
	Components::ensureString();
})->throws(\ArgumentCountError::class);


/**
 * Components::classnames tests
 */
test('Asserts classnames returns a string', function ($args) {
	$this->assertIsString(Components::classnames($args));
})->with('classesArray');


test('Throws type exception if wrong argument type is passed to classnames',
	function ($argument) {
		Components::classnames($argument);
	})
	->throws(\TypeError::class)
	->with('errorStringArguments');


/**
 * Components::getManifest tests
 */
test('Asserts that reading manifest.json using getManifest will return an array', function () {
	$results = Components::getManifest(dirname(__FILE__, 2) . '/data');

	$this->assertIsArray($results, 'The result is not an array');
	$this->assertArrayHasKey('application.css', $results, 'Missing a key from the manifest.json file');
});


test('Asserts that not specifying the path in getManifest will throw an exception', function () {
	Components::getManifest(dirname(__FILE__));
})->throws(ComponentException::class);


test('Asserts that providing wrong type to getManifest will throw an exception', function () {
	Components::getManifest(['path']);
})->throws(\TypeError::class);


/**
 * Components::render tests
 */
test('Asserts that rendering a component works', function () {
	$results = Components::render('component.php', []);

	$this->assertNotEmpty($results, 'Component should be rendered here');
	$this->assertStringContainsString('Hello!', $results, 'Component should contain a string');
});

test('Asserts that rendering a component will output a wrapper if parentClass is provided', function () {
	$results = Components::render('component.php', ['parentClass' => 'test']);

	$this->assertNotEmpty($results, 'Component should be rendered here');
	$this->assertStringContainsString('Hello!', $results, 'Component should contain a string');
	$this->assertStringNotContainsString('test__component.php', $results, 'Component should contain a class name, not file type');
	$this->assertStringContainsString('test__component', $results, 'Component should contain a class name');
});


test('Asserts that providing a missing component will throw an exception without extension', function () {
	Components::render('component', []);
})->throws(ComponentException::class);


test('Asserts that providing a missing component will throw an exception', function () {
	Components::render('component-a.php', []);
})->throws(ComponentException::class);

