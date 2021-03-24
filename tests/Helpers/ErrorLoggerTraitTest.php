<?php

namespace Tests\Helpers;

use EightshiftLibs\Helpers\ErrorLoggerTrait;

use function Tests\setupMocks;
use function Tests\mock;

class MockErrorLogger {
    use ErrorLoggerTrait;
}

beforeEach(function () {
    setupMocks();

	$this->mockLogger = new MockErrorLogger();
});


test('Test restResponseHandler will return expected result in case of success', function () {
    $response = $this->mockLogger->restResponseHandler(200, 'Some status', 'Some message', ['fake data']);

    $this->assertJson($response, 'Response is not a JSON string.');
    $this->assertEquals('{"success":true,"data":{"code":200,"status":"Some status","message":"Some message","data":["fake data"]}}', $response);
});


test('Test restResponseHandler will return expected result in case of error', function () {
    mock('WP_Error');

    $response = $this->mockLogger->restResponseHandler(404, 'Some error', 'Some error message', ['fake data']);

    $this->assertJson($response, 'Response is not a JSON string.');
    $this->assertEquals('{"success":false,"data":{}}', $response);
});
