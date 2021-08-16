<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\FailedToLoadView;

use Brain\Monkey;

use function Tests\setupMocks;

beforeAll(function () {
	Monkey\setUp();
	setupMocks();
});

afterAll(function() {
	Monkey\tearDown();
});

test('Checks if the viewException method will return correct response.', function () {

	$uri = 'https://random.uri';
	$exception = new \Exception('Error message');

	$exceptionObject = FailedToLoadView::viewException($uri, $exception);

	$this->assertIsObject($exceptionObject, "The {$exceptionObject} should be an instance of FailedToLoadView class");
	$this->assertObjectHasAttribute('message', $exceptionObject, "Object doesn't contain message attribute");
	$this->assertSame("Could not load the View URI: {$uri}. Reason: {$exception->getMessage()}.", $exceptionObject->getMessage(), "Strings for exception messages do not match!");
});
