<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\InvalidService;

use Brain\Monkey;

use function Tests\setupMocks;

beforeAll(function () {
	Monkey\setUp();
	setupMocks();
});

afterAll(function() {
	Monkey\tearDown();
});

test('Checks if the fromService method will return correct response.', function () {

	$service = 'nonexistent';

	$exceptionObject = InvalidService::fromService($service);

	$this->assertIsObject($exceptionObject, "The {$exceptionObject} should be an instance of InvalidService class");
	$this->assertObjectHasAttribute('message', $exceptionObject, "Object doesn't contain message attribute");
	$this->assertSame("The service {$service} is not recognized and cannot be registered.", $exceptionObject->getMessage(), "Strings for message if service name isn't recognised do not match!");
});
