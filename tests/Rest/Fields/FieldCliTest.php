<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\Rest\Fields\FieldCli;

use function Tests\deleteCliOutput;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = \Mockery::mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$this->field = new FieldCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});


test('REST field CLI command will correctly copy the field class with defaults', function () {
	$field = $this->field;
	$field([], $field->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedField = file_get_contents(dirname(__FILE__, 4) . '/cliOutput/src/Rest/Fields/TitleField.php');

	$this->assertStringContainsString('class TitleField extends AbstractField implements CallableFieldInterface', $generatedField);
	$this->assertStringContainsString('return \'title\';', $generatedField);
	$this->assertStringContainsString('return \'post\';', $generatedField);
	$this->assertStringContainsString('get_callback', $generatedField);
	$this->assertStringContainsString('rest_ensure_response', $generatedField);
});

test('REST field CLI command will correctly copy the field class with arguments', function ($fieldNameArguments) {
	$field = $this->field;
	$field([], $fieldNameArguments);
	$full_field_name = "{$this->field->getFileName($fieldNameArguments['field_name'])}Field";
	$object_type = $fieldNameArguments['object_type'];

	// Check the output dir if the generated method is correctly generated.
	$generatedField = file_get_contents(dirname(__FILE__, 4) . "/cliOutput/src/Rest/Fields/{$full_field_name}.php");

	$this->assertStringContainsString("class {$full_field_name} extends AbstractField implements CallableFieldInterface", $generatedField);
	$this->assertStringContainsString("return '{$object_type}';", $generatedField);
	$this->assertStringNotContainsString('example-post-type', $generatedField);
	$this->assertStringNotContainsString('example-field', $generatedField);
})->with('correctFieldNameArguments');

test('REST field CLI command will throw error on missing / invalid arguments', function ($fieldNameArguments) {
	$field = $this->field;
	$field([], $fieldNameArguments);
})->with('errorFieldNameArguments')->throws('exception');
