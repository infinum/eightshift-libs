<?php

namespace Tests\Unit\Enqueue\Theme;

use EightshiftLibs\Enqueue\Theme\EnqueueThemeCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new EnqueueThemeCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Custom enqueue theme CLI command will correctly copy the Enqueue Theme class', function () {
	$theme = $this->mock;
	$theme([], $theme->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$generatedTheme = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/Enqueue/Theme/EnqueueTheme.php');

	$this->assertStringContainsString('class EnqueueTheme extends AbstractEnqueueTheme', $generatedTheme);
	$this->assertStringContainsString('wp_enqueue_scripts', $generatedTheme);
	$this->assertStringNotContainsString('admin_enqueue_scripts', $generatedTheme);
});


test('Custom Enqueue Theme CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
