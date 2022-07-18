<?php

namespace Tests\Unit\WpCli;

use EightshiftBoilerplate\WpCli\WpCliExample;

beforeEach(function () {
	$this->mock = new WpCliExample('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Register method will call init hook', function () {
	$this->mock->register();

	expect(has_action('cli_init', 'EightshiftBoilerplate\WpCli\WpCliExample->registerCommand()'))
		->toEqual(10);
});

test('Prepare command docs returns correct doc', function() {
	$mock = $this->mock->getDocs();

	expect($mock)
		->toHaveKeys(['shortdesc']);
});

test('Custom command class is callable', function() {
	$customCommand = $this->mock;

	expect($customCommand)->toBeCallable();
});


test('Custom command example documentation is correct', function () {
	expect($this->mock->getDocs())->toBeArray();
});
