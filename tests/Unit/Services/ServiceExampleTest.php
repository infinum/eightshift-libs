<?php

namespace Tests\Unit\Login;

use EightshiftBoilerplate\Services\ServiceExample;

beforeEach(function() {
	$this->service = new ServiceExample();
});

afterEach(function () {
	unset($this->service);
});

test('Service Example test register method', function () {
	$this->assertNull($this->service->register());
});

test('Service Example contains register method', function () {
	$this->assertTrue(\method_exists($this->service, 'register'));
});
