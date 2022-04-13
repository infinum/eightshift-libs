<?php

namespace Tests\Unit\View;

use Brain\Monkey;
use EightshiftBoilerplate\View\EscapedViewExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->example = new EscapedViewExample();
});

afterEach(function() {
	Monkey\tearDown();
});


test('Escaped view class has register method', function () {
	$this->assertTrue(\method_exists($this->example, 'register'));
	$this->assertEmpty($this->example->register());
});
