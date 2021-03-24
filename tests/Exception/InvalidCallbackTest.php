<?php

namespace Tests\Unit\Exception;

use Brain\Monkey;
use Brain\Monkey\Functions;

use EightshiftLibs\Exception\InvalidCallback;
use stdClass;

use function Tests\setupMocks;

beforeAll(function () {
	setupMocks();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Create a new instance of the exception for a callback class name that is not recognized.', function () {

	$callback = 'randomText';

	$exceptionObject = InvalidCallback::fromCallback($callback);

	$this->assertIsObject($exceptionObject);
	$this->assertObjectHasAttribute('message', $exceptionObject);
	$this->assertStringContainsString("The callback {$callback} is not recognized and cannot be registered.", $exceptionObject->getMessage());

});