<?php

namespace Tests\Unit\View;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\View\EscapedViewCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new EscapedViewCli('boilerplate');
});

afterEach(function () {
	setAfterEach();
});

test('Escaped view command will correctly copy the EscapedView class with defaults', function () {
	$mock = $this->mock;
	$mock([], $mock->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$sep = \DIRECTORY_SEPARATOR;
	$mock = \file_get_contents(Components::getProjectPaths('cliOuput', "src{$sep}View{$sep}EscapedView.php"));

	$this->assertNotEmpty($mock);
	$this->assertStringContainsString('class EscapedView extends AbstractEscapedView implements ServiceInterface', $mock);
	$this->assertStringContainsString('register', $mock);
	expect($mock)->not-> toContain('someRandomMethod');
});

test('Escaped view CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
