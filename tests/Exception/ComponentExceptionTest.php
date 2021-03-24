<?php

namespace Tests\Unit\Enqueue\Exception;

use Brain\Monkey;
use Brain\Monkey\Functions;

use EightshiftLibs\Exception\ComponentException;
use stdClass;

use function Tests\setupMocks;

beforeAll(function () {
	setupMocks();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Throws exception if ensure_string argument is invalid.', function () {

	$object = new stdClass();

	$exceptionObject = ComponentException::throwNotStringOrArray($object);
	$exceptionInteger = ComponentException::throwNotStringOrArray(7);
	$exceptionNull = ComponentException::throwNotStringOrArray(null);
	$exceptionBool = ComponentException::throwNotStringOrArray(true);
	
	$this->assertObjectHasAttribute('message', $exceptionObject);
	$this->assertObjectHasAttribute('message', $exceptionInteger);
	$this->assertObjectHasAttribute('message', $exceptionNull);
	$this->assertObjectHasAttribute('message', $exceptionBool);
	// $this->assertAttributeEquals('Object couldn\'t be converted to string. Please provide only string or array.', 'message', $exception);
});

test('Throws exception if unable to locate component.', function () {

	$component = 'nonexistent';
	$output = ComponentException::throwUnableToLocateComponent($component);

	$this->assertStringContainsString("Unable to locate component by path: {$component}", $output);
});