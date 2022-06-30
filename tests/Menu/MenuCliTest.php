<?php

namespace Tests\Unit\Menu;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Menu\MenuCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new MenuCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Menu CLI command will correctly copy the Menu class with defaults', function () {
	$mock = $this->mock;
	$mock([], []);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOuput', "src{$sep}Menu{$sep}Menu.php"));

	$this->assertStringContainsString('class Menu extends AbstractMenu', $output);
	$this->assertStringContainsString('header_main_nav', $output);
	$this->assertStringNotContainsString('rendom string', $output);
});

test('Menu CLI command will correctly copy the Menu class with set arguments', function () {
	$mock = $this->mock;
	$mock([], [
		'namespace' => 'CoolTheme',
	]);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOuput', "src{$sep}Menu{$sep}Menu.php"));

	$this->assertStringContainsString('class Menu extends AbstractMenu', $output);
	$this->assertStringContainsString('namespace CoolTheme\Menu;', $output);
	$this->assertStringContainsString('header_main_nav', $output);
	$this->assertStringNotContainsString('rendom string', $output);
});

test('Menu CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});

