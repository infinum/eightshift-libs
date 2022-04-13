<?php

namespace Tests\Unit\CustomMeta;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\CustomMeta\AcfMetaExample;

use function Tests\setupUnitTestMocks;

beforeEach(function() {
	Monkey\setUp();
	setupUnitTestMocks();

	$this->example = new AcfMetaExample();
});

afterEach(function() {
	Monkey\tearDown();
});


test('Register method will bail out if ACF is not registered/activated', function () {
	$this->assertNull($this->example->register());
});


test('Register method will call acf init hook', function () {
	Functions\when('is_admin')->justReturn(true);

	$this->getMockBuilder(\ACF::class)->getMock();

	$this->example->register();

	$this->assertSame(10, has_action('acf/init', 'EightshiftBoilerplate\CustomMeta\AcfMetaExample->fields()'));
});
