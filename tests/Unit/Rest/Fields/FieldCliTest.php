<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Rest\Fields\FieldCli;

beforeEach(function () {
	$this->mock = new FieldCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('REST field CLI command will correctly copy the field class with defaults', function () {
	$mock = $this->mock;
	$mock([], $mock->getDefaultArgs());

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "src{$sep}Rest{$sep}Fields{$sep}TitleCustomField.php"));

	$this->assertStringContainsString('class TitleCustomField extends AbstractField implements CallableFieldInterface', $output);
	$this->assertStringContainsString('return \'title-custom\';', $output);
	$this->assertStringContainsString('return \'example\';', $output);
	$this->assertStringContainsString('get_callback', $output);
	$this->assertStringContainsString('rest_ensure_response', $output);
	$this->assertStringNotContainsString('ExampleRoute', $output);
});

test('REST field CLI command will correctly copy the field class with arguments', function ($mockNameArguments) {
	$mock = $this->mock;
	$mock([], $mockNameArguments);

	$fullFieldName = "{$this->mock->getFileName($mockNameArguments['field_name'])}Field";
	$objectType = $mockNameArguments['object_type'];

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "src{$sep}Rest{$sep}Fields{$sep}{$fullFieldName}.php"));

	$this->assertStringContainsString("class {$fullFieldName} extends AbstractField implements CallableFieldInterface", $output);
	$this->assertStringContainsString("return '{$objectType}';", $output);
	$this->assertStringNotContainsString('example-post-type', $output);
	$this->assertStringNotContainsString('example-field', $output);
})->with('correctFieldNameArguments');
