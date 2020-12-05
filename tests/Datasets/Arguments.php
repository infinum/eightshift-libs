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
