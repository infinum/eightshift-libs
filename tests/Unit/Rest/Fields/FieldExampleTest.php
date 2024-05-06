<?php

namespace Tests\Unit\CustomPostType;

use Brain\Monkey\Functions;
use EightshiftLibs\Rest\Fields\FieldCli;
use Infinum\Rest\Fields\TitleCustomField;

use function Tests\getMockArgs;
use function Tests\mock;
use function Tests\reqOutputFiles;

beforeEach(function() {
	$fieldCliMock = new FieldCli('boilerplate');
	$fieldCliMock([], getMockArgs($fieldCliMock->getDefaultArgs()));

	reqOutputFiles(
		'Config/Config.php',
		'Rest/Fields/TitleCustomField.php',
	);

	$this->wpRestServer = mock('alias:WP_REST_Server');
});

afterEach(function () {
	unset(
		$this->field,
		$this->wpRestServer
	);
});

test('Register method will call init hook', function () {
	(new TitleCustomField())->register();

	$this->assertSame(10, has_action('rest_api_init', 'Infinum\Rest\Fields\TitleCustomField->fieldRegisterCallback()'));
});

test('Field has a valid callback', function () {
	$output = (new TitleCustomField())->fieldCallback(new class{}, 'attr', new class{}, 'post');

	$this->assertStringContainsString($output, 'output data');
});

test('Field registers the callback properly', function () {
	$action = 'field_registered';
	Functions\when('register_rest_field')->justReturn(putenv("SIDEAFFECT={$action}"));

	(new TitleCustomField())->fieldRegisterCallback($this->wpRestServer, 'attr', new class{}, 'post');

	$this->assertSame(\getenv('SIDEAFFECT'), $action);

	// Cleanup.
	putenv('SIDEAFFECT=');
});
