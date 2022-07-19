<?php

namespace Tests\Unit\View;

use EightshiftBoilerplate\View\EscapedViewExample;

beforeEach(function() {
	$this->example = new EscapedViewExample();
});

afterEach(function () {
	unset($this->example);
});

test('Escaped view class has register method', function () {
	$this->assertTrue(\method_exists($this->example, 'register'));
	$this->assertEmpty($this->example->register());
});
