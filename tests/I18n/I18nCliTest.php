<?php

namespace Tests\Unit\I18n;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\I18n\I18nCli;

use function Tests\deleteCliOutput;
use function Tests\mock;
use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new I18nCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('I18n CLI command will correctly copy the I18n class with defaults', function () {
	$mock = $this->mock;
	$mock([], []);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOuput', "src{$sep}I18n{$sep}I18n.php"));

	$this->assertStringContainsString('class I18n implements ServiceInterface', $output);
	$this->assertStringContainsString('@package EightshiftLibs\I18n', $output);
	$this->assertStringContainsString('namespace EightshiftLibs\I18n', $output);
	$this->assertStringNotContainsString('footer.php', $output);
});

test('I18n CLI command will correctly copy the I18n class with set arguments', function () {
	$mock = $this->mock;
	$mock([], [
		'namespace' => 'CoolTheme',
	]);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOuput', "src{$sep}I18n{$sep}I18n.php"));

	$this->assertStringContainsString('namespace CoolTheme\I18n;', $output);
});

test('I18n CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
