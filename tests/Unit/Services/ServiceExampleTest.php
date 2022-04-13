<?php

namespace Tests\Unit\Login;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Services\ServiceExample;

use function Tests\setupUnitTestMocks;

beforeEach(function() {
	Monkey\setUp();
	setupUnitTestMocks();

	$this->service = new ServiceExample();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Service Example test register method', function () {
	$this->assertNull($this->service->register());
});

test('Service Example contains register method', function () {
	$this->assertTrue(\method_exists($this->service, 'register'));
});
