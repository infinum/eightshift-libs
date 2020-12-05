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
	['bar', 'foo']
]);
