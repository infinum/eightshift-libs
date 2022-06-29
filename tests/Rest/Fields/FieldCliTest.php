<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Rest\Fields\FieldCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new FieldCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});


test('REST field CLI command will correctly copy the field class with defaults', function () {
	$mock = $this->mock;
	$mock([], $mock->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('testsOutput', "src{$sep}ThemeOptions{$sep}ThemeOptions.php"));
	$generatedField = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/Rest/Fields/TitleField.php');

	$this->assertStringContainsString('class TitleField extends AbstractField implements CallableFieldInterface', $generatedField);
	$this->assertStringContainsString('return \'title\';', $generatedField);
	$this->assertStringContainsString('return \'post\';', $generatedField);
	$this->assertStringContainsString('get_callback', $generatedField);
	$this->assertStringContainsString('rest_ensure_response', $generatedField);
	$this->assertStringNotContainsString('ExampleRoute', $generatedField);
});

test('REST field CLI command will correctly copy the field class with arguments', function ($mockNameArguments) {
	$mock = $this->mock;
	$mock([], $mockNameArguments);

	$fullFieldName = "{$this->mock->getFileName($mockNameArguments['field_name'])}Field";
	$objectType = $mockNameArguments['object_type'];

	// Check the output dir if the generated method is correctly generated.
	$generatedField = \file_get_contents(\dirname(__FILE__, 4) . "/cliOutput/src/Rest/Fields/{$fullFieldName}.php");

	$this->assertStringContainsString("class {$fullFieldName} extends AbstractField implements CallableFieldInterface", $generatedField);
	$this->assertStringContainsString("return '{$objectType}';", $generatedField);
	$this->assertStringNotContainsString('example-post-type', $generatedField);
	$this->assertStringNotContainsString('example-field', $generatedField);
})->with('correctFieldNameArguments');
