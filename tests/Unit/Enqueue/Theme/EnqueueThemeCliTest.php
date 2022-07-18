<?php

namespace Tests\Unit\Enqueue\Theme;

use EightshiftLibs\Enqueue\Theme\EnqueueThemeCli;
use EightshiftLibs\Helpers\Components;

beforeEach(function () {
	$this->mock = new EnqueueThemeCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Custom enqueue theme CLI command will correctly copy the Enqueue Theme class', function () {
	$theme = $this->mock;
	$theme([], $theme->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$generatedTheme = \file_get_contents(Components::getProjectPaths('srcDestination', 'Enqueue/Theme/EnqueueTheme.php'));

	expect($generatedTheme)
		->toContain('class EnqueueTheme extends AbstractEnqueueTheme')
		->toContain('wp_enqueue_scripts')
		->not->toContain('admin_enqueue_scripts');
});


test('Custom Enqueue Theme CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
