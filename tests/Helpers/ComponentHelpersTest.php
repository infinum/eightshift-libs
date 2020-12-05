<?php

namespace Tests\Unit\Helpers;

use EightshiftLibs\Helpers\Components;

test('Asserts ensure string returns a correct result', function ($args) {
	$this->assertIsString(Components::ensureString($args));
})->with('correctArguments');

test('Throws type exception if wrong argument type is passed to ensureString',
	function ($argument) {
		Components::ensureString($argument);
	})
	->throws(\TypeError::class)
	->with('errorStringArguments');

test('Throws argument count exception if no argument is passed', function () {
	Components::ensureString();
})->throws(\ArgumentCountError::class);

test('Asserts classnames returns a string', function ($args) {
	error_log(print_r(Components::classnames($args), true));
	$this->assertIsString(Components::classnames($args));
})->with('classesArray');

test('Throws type exception if wrong argument type is passed to classnames',
	function ($argument) {
		Components::classnames($argument);
	})
	->throws(\TypeError::class)
	->with('errorStringArguments');
