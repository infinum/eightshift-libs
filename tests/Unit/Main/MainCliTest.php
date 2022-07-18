<?php

namespace Tests\Unit\Main;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Main\MainCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new MainCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Main CLI command will correctly copy the Main class with defaults', function () {
	$mock = $this->mock;
	$mock([], []);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "src{$sep}Main{$sep}Main.php"));

	$this->assertStringContainsString('class Main extends AbstractMain', $output);
	$this->assertStringContainsString('@package EightshiftLibs\Main', $output);
	$this->assertStringContainsString('namespace EightshiftLibs\Main', $output);
	$this->assertStringNotContainsString('footer.php', $output);
});

test('Main CLI command will correctly copy the Main class with set arguments', function () {
	$mock = $this->mock;
	$mock([], [
		'namespace' => 'CoolTheme',
	]);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "src{$sep}Main{$sep}Main.php"));

	$this->assertStringContainsString('namespace CoolTheme\Main', $output);
	$this->assertStringNotContainsString('namespace EightshiftLibs\Main', $output);
});

test('Main CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
