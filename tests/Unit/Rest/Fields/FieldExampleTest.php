<?php

namespace Tests\Unit\CustomPostType;

use Brain\Monkey\Functions;
use EightshiftBoilerplate\Rest\Fields\FieldExample;

use function Tests\mock;

beforeEach(function() {
	$this->field = new FieldExample();

	$this->wpRestServer = mock('alias:WP_REST_Server');
});

afterEach(function () {
	unset($this->field, $this->wpRestServer);
});

test('Register method will call init hook', function () {
	$this->field->register();

	$this->assertSame(10, has_action('rest_api_init', 'EightshiftBoilerplate\Rest\Fields\FieldExample->fieldRegisterCallback()'));
});

test('Field has a valid callback', function () {
	$output = $this->field->fieldCallback(new class{}, 'attr', new class{}, 'post');

	$this->assertStringContainsString($output, 'output data');
});

test('Field registers the callback properly', function () {
	$action = 'field_registered';
	Functions\when('register_rest_field')->justReturn(putenv("SIDEAFFECT={$action}"));

	$this->field->fieldRegisterCallback($this->wpRestServer, 'attr', new class{}, 'post');

	$this->assertSame(\getenv('SIDEAFFECT'), $action);

	// Cleanup.
	putenv('SIDEAFFECT=');
});
