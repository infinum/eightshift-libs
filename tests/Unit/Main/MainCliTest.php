<?php

namespace Tests\Unit\Main;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Main\MainCli;

use function Tests\getMockArgs;

beforeEach(function () {
	$this->mock = new MainCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Main CLI command will correctly copy the Main class with defaults', function () {
	$mock = $this->mock;
	$mock([], getMockArgs());

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Helpers::getProjectPaths('srcDestination', "Main{$sep}Main.php"));

	$this->assertStringContainsString('class Main extends AbstractMain', $output);
	$this->assertStringContainsString('@package Infinum\Main', $output);
	$this->assertStringContainsString('namespace Infinum\Main', $output);
	$this->assertStringNotContainsString('footer.php', $output);
});

test('Main CLI command will correctly copy the Main class with set arguments', function () {
	$mock = $this->mock;
	$mock([], getMockArgs([
		'namespace' => 'CoolTheme',
	]));

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Helpers::getProjectPaths('srcDestination', "Main{$sep}Main.php"));

	$this->assertStringContainsString('namespace CoolTheme\Main', $output);
	$this->assertStringNotContainsString('namespace EightshiftLibs\Main', $output);
});

test('Main CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
