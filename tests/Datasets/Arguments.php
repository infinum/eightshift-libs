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

// Rest/Fields arguments
dataset('correctFieldNameArguments', [
	[['field_name' => 'SomeField', 'object_type' => 'post']],
	[['field_name' => 'some field', 'object_type' => 'post']],
	[['field_name' => 'some-field', 'object_type' => 'post']],
	[['field_name' => 'some_field', 'object_type' => 'post']],
	[['field_name' => 'Some Field', 'object_type' => 'page']],
	[['field_name' => 'Some Field', 'object_type' => 'custom-post-type']],
]);

dataset('errorFieldNameArguments', [
	[['field_name' => 'SomeField']],
	[['object_type' => 'post']],
]);

// Rest/Routes arguments
dataset('correctRouteArguments', [
	[['endpoint_slug' => 'some-test', 'method' => 'post']],
	[['endpoint_slug' => 'some-test', 'method' => 'get']],
	[['endpoint_slug' => 'some-test', 'method' => 'put']],
	[['endpoint_slug' => 'some-test', 'method' => 'patch']],
	[['endpoint_slug' => 'some-test', 'method' => 'delete']],
	[['endpoint_slug' => 'someTest', 'method' => 'post']],
	[['endpoint_slug' => 'some_test', 'method' => 'post']],
	[['endpoint_slug' => 'some_Test_1', 'method' => 'post']],
]);

dataset('errorRouteArguments', [
	[['endpoint_slug' => 'some-test']],
	[['method' => 'post']],
]);

dataset('invalidRouteArguments', [
	[['endpoint_slug' => 'some-test', 'method' => 'asdad']],
	[['endpoint_slug' => '', 'method' => 'post']],
]);

dataset('inputSlugs', [
	'someName',
	'some-Name',
	'some name',
	'longer slug goes here',
	'mixed_Case_goes here-as well',
	'UPPER-CASE',
]);

// Exceptions
dataset('exceptionArguments', [
	7,
	null,
	true
]);
