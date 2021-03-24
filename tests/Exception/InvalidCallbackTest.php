<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\InvalidCallback;

use function Tests\setupMocks;

beforeAll(function () {
	setupMocks();
});

test('Create a new instance of the exception for a callback class name that is not recognized.', function () {

	$callback = 'randomText';

	$exceptionObject = InvalidCallback::fromCallback($callback);

	$this->assertIsObject($exceptionObject);
	$this->assertObjectHasAttribute('message', $exceptionObject);
	$this->assertStringContainsString("The callback {$callback} is not recognized and cannot be registered.", $exceptionObject->getMessage());
});
