<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\ComponentException;
use stdClass;

test('Checks if the throwNotStringOrArray method functions correctly.',
	function ($argument) {
		$exceptionObject = ComponentException::throwNotStringOrArray($argument);
		$type = \gettype($argument);

		$this->assertIsObject($exceptionObject, "The {$exceptionObject} should be an instance of ComponentException class");
		$this->assertObjectHasProperty('message', $exceptionObject, "Object doesn't contain message attribute");
		$this->assertSame("{$argument} variable is not a string or array but rather {$type}", $exceptionObject->getMessage(), "Strings for message if item is {$type} do not match!");

	})
	->with('exceptionArguments');

	test('Checks if the throwNotStringOrArray method functions correctly with objects.',
	function () {

		$object = new stdClass();
		$exceptionObject = ComponentException::throwNotStringOrArray($object);

		$this->assertIsObject($exceptionObject, "The object should be an instance of ComponentException class");
		$this->assertObjectHasProperty('message', $exceptionObject, "Object doesn't contain message attribute");
		$this->assertSame('Object couldn\'t be converted to string. Please provide only string or array.', $exceptionObject->getMessage(), "Strings for 'Object couldn't be converted to string' message do not match!");

	});

test('Checks if throwUnableToLocateComponent method will return correct response.', function () {

	$component = 'nonexistent';
	$output = ComponentException::throwUnableToLocateComponent($component);

	$this->assertIsObject($output, "The {$output} should be an instance of ComponentException class");
	$this->assertObjectHasProperty('message', $output, "Object doesn't contain message attribute");
	$this->assertSame("Unable to locate component by path: {$component}", $output->getMessage(), "Strings for 'Unable to locate component by path' message do not match!");
});
