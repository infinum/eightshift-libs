<?php

namespace Tests\Unit\Helpers;

use EightshiftLibs\Helpers\Components;

use Brain\Monkey;

use function Tests\setupMocks;

beforeAll(function () {
	Monkey\setUp();
	setupMocks();
});

afterAll(function() {
	Monkey\tearDown();
});

beforeEach(function () {
	global $shortcode_tags;
	$shortcode_tags = ['sayHello' => 'Tests\\Unit\\Helpers\\sayHello'];

	$this->shortcode = new Components();
});


test('Shortcode helper will call the shortcode callback', function() {
	function sayHello(array $args): void {
		echo "Hello {$args['name']}!";
	}
	
	\ob_start();
	$this->shortcode->getShortcode('sayHello', ['name' => 'John']);
	$result = \ob_get_clean();

	$this->assertSame('Hello John!', $result);
});


test('Shortcode helper will return false in case the callback doesn\'t exist', function() {
	$this->assertFalse($this->shortcode->getShortcode('random', ['name' => 'John']));
});
