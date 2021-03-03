<?php

dataset('errorStringArguments', [
	123123,
	false,
	null,
	[true],
	[true, null],
	new class{}
]);

dataset('correctArguments', [
	'simple',
	'string with spaces',
	'String With special characters +_^&%$#@!9',
	[['bar', 'foo']],
	[['key' => 'bar', 'other-key' => 'foo']],
	[['key' => 'key']]
]);

dataset('classesArray', [
	[['medium', 'large']],
	[['small']],
	[['key' => 'bold--all']]
]);

dataset('correctFieldNameArguments', [
	[['field_name' => 'NekiField', 'object_type' => 'post']],
	[['field_name' => 'neki field', 'object_type' => 'post']],
	[['field_name' => 'neki-field', 'object_type' => 'post']],
	[['field_name' => 'neki_field', 'object_type' => 'post']],
	[['field_name' => 'Neki Field', 'object_type' => 'page']],
	[['field_name' => 'Neki Field', 'object_type' => 'custom-post-type']],
]);

dataset('errorFieldNameArguments', [
	[['field_name' => 'NekiField']],
	[['object_type' => 'post']],
]);
