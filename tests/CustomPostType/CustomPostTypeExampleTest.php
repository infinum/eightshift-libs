<?php

namespace Tests\Unit\CustomPostType;

use Brain\Monkey;
use Brain\Monkey\Functions;
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


/**
 * This is a tricky one. Because it should be an integration test:
 * testing if the CPT was actually registered when the action runs.
 *
 * But we can kinda test this. Since the postTypeRegisterCallback doesn't have a return (void),
 * we can try to mock the register_post_type to introduce a temporary side-affect. Like creating a file, or setting an
 * environment variable on the fly. That way, when we actually call the method, we know that it will be called.
 */
test('Register post type method will be called', function() {
	$action = 'post_type_registered';
	Functions\when('register_post_type')->justReturn(putenv("SIDEAFFECT={$action}"));

	(new PostTypeExample())->postTypeRegisterCallback();

	$this->assertEquals(getenv('SIDEAFFECT'), $action);

	// Cleanup.
	putenv('SIDEAFFECT=');
});
