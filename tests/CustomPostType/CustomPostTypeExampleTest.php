<?php

namespace Tests\Unit\CustomPostType;

use Brain\Monkey;
use EightshiftBoilerplate\CustomPostType\PostTypeExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();
});

afterEach(function() {
	Monkey\tearDown();
});


test('Register method will call init hook', function () {
	(new PostTypeExample())->register();

	$this->assertSame(10, has_action('init', 'EightshiftBoilerplate\CustomPostType\PostTypeExample->postTypeRegisterCallback()'));
});
