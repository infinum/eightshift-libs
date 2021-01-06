<?php

namespace Tests\Helpers;

use EightshiftLibs\Helpers\ErrorLoggerTrait;

class MockErrorLogger {
    use ErrorLoggerTrait;
}

test('Test restResponseHandler will return expected result in case of success', function () {
    $mockLogger = new MockErrorLogger();

    $response = $mockLogger->restResponseHandler(200, 'Some status', 'Some message', ['fake data']);

    $this->assertJson($response, 'Response is not a JSON string.');
    $this->assertEquals('[{"code":200,"status":"Some status","message":"Some message","data":["fake data"]},200]', $response);
});

test('Test restResponseHandler will return expected result in case of error', function () {
    $mockLogger = new MockErrorLogger();
    $mockError = \Mockery::mock('WP_Error');

    $response = $mockLogger->restResponseHandler(404, 'Some error', 'Some error message', ['fake data']);

    $this->assertJson($response, 'Response is not a JSON string.');
    $this->assertEquals('[{},404]', $response);
});
