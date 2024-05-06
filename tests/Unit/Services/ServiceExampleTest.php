<?php

namespace Tests\Unit\Login;

use EightshiftLibs\Services\ServiceExampleCli;

beforeEach(function() {
	$this->mock = new ServiceExampleCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Service Example test register method', function () {
	$this->assertNull($this->mock->register());
});

test('Service Example contains register method', function () {
	$this->assertTrue(\method_exists($this->mock, 'register'));
});
